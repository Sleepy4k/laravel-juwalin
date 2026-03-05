<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContainerStatus;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Services\ProxmoxApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ContainerController extends Controller
{
    public function __construct(private readonly ProxmoxApiService $proxmox) {}

    public function index(Request $request): View
    {
        $query = Container::with(['user', 'order.package'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', static fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $containers = $query->paginate(20)->withQueryString();

        return view('admin.containers.index', compact('containers'));
    }

    public function show(Container $container): View
    {
        $container->load(['user', 'order.package', 'portForwardingRequests']);

        // Sync live status from Proxmox into DB so admin sees accurate state.
        if ($container->vmid && !$container->isProvisioning()) {
            try {
                $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
                $synced = $this->mapProxmoxStatus($live['status'] ?? 'stopped');

                if ($container->status !== $synced) {
                    $container->update(['status' => $synced]);
                    $container->status = $synced;
                }
            } catch (Throwable) {
                // Proxmox unreachable — continue with stale status
            }
        }

        return view('admin.containers.show', compact('container'));
    }

    /**
     * GET /admin/containers/{container}/status  (JSON, polled by JS).
     */
    public function status(Container $container): JsonResponse
    {
        if (!$container->vmid || $container->isProvisioning()) {
            return response()->json([
                'status' => $container->status->value,
                'label'  => $container->status->label(),
                'color'  => $container->status->color(),
            ]);
        }

        try {
            $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
            $synced = $this->mapProxmoxStatus($live['status'] ?? 'stopped');

            if ($container->status !== $synced) {
                $container->update(['status' => $synced]);
                $container->status = $synced;
            }
        } catch (Throwable) {
        }

        return response()->json([
            'status' => $container->status->value,
            'label'  => $container->status->label(),
            'color'  => $container->status->color(),
        ]);
    }

    /**
     * GET /admin/containers/{container}/console.
     */
    public function console(Container $container): View
    {
        try {
            $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
            abort_if(($live['status'] ?? '') !== 'running', 422, 'Container sedang tidak berjalan.');
        } catch (Throwable $e) {
            abort(503, 'Tidak dapat terhubung ke Proxmox: ' . $e->getMessage());
        }

        return view('admin.containers.console', compact('container'));
    }

    /**
     * GET /admin/containers/{container}/vnc-url
     * Returns a fresh wss:// URL with a new VNC ticket as JSON.
     * Called by the console view after noVNC loads to avoid ticket expiry.
     */
    public function vncUrl(Container $container): JsonResponse
    {
        try {
            $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
            abort_if(($live['status'] ?? '') !== 'running', 422, 'Container sedang tidak berjalan.');
        } catch (Throwable $e) {
            abort(503, 'Tidak dapat terhubung ke Proxmox: ' . $e->getMessage());
        }

        try {
            $vncProxy = $this->proxmox->getVncProxy($container->vmid, $container->node);
        } catch (Throwable $e) {
            abort(503, 'Gagal membuat sesi VNC: ' . $e->getMessage());
        }

        return response()->json([
            'wsUrl' => $this->buildVncWsUrl($container->vmid, $container->node, $vncProxy),
        ]);
    }

    /**
     * GET /admin/containers/{container}/term-url
     * Creates a termproxy session and returns a fresh wss:// URL + ticket.
     * The ticket must be sent as the first WebSocket message to authenticate.
     */
    public function termUrl(Container $container): JsonResponse
    {
        try {
            $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
            abort_if(($live['status'] ?? '') !== 'running', 422, 'Container sedang tidak berjalan.');
        } catch (Throwable $e) {
            abort(503, 'Tidak dapat terhubung ke Proxmox: ' . $e->getMessage());
        }

        try {
            $termProxy = $this->proxmox->getTermProxy($container->vmid, $container->node);
        } catch (Throwable $e) {
            abort(503, 'Gagal membuat sesi terminal: ' . $e->getMessage());
        }

        return response()->json([
            'wsUrl'  => $this->buildVncWsUrl($container->vmid, $container->node, $termProxy),
            'ticket' => $termProxy['ticket'] ?? '',
        ]);
    }

    public function action(Request $request, Container $container): RedirectResponse
    {
        $request->validate(['action' => ['required', 'in:start,stop,shutdown,restart']]);

        $node = $container->node ?? config('proxmox.node');

        // Guard actions against live Proxmox state to prevent spurious errors.
        try {
            $live = $this->proxmox->getContainerStatus($container->vmid, $node);
            $isRunning = ($live['status'] ?? '') === 'running';

            match ($request->action) {
                'start' => abort_if($isRunning, 422, 'Container sudah berjalan.'),
                'stop', 'shutdown', 'restart' => abort_if(!$isRunning, 422, 'Container tidak sedang berjalan.'),
            };
        } catch (Throwable) {
            // Proxmox unreachable for status check — allow action to proceed
        }

        match ($request->action) {
            'start'    => $this->proxmox->startContainer($container->vmid, $node),
            'stop'     => $this->proxmox->stopContainer($container->vmid, $node),
            'shutdown' => $this->proxmox->shutdownContainer($container->vmid, node: $node),
            'restart'  => $this->proxmox->rebootContainer($container->vmid, $node),
        };

        // Optimistic DB sync
        $newStatus = match ($request->action) {
            'start' => ContainerStatus::Running,
            'stop', 'shutdown' => ContainerStatus::Stopped,
            default => $container->status,
        };
        $container->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Aksi berhasil dikirim ke Proxmox.');
    }

    public function destroy(Container $container): RedirectResponse
    {
        $container->delete();

        return redirect()->route('admin.containers.index')->with('success', 'Container dihapus dari database.');
    }

    /**
     * Map a Proxmox status string to our ContainerStatus enum.
     */
    private function mapProxmoxStatus(string $proxmoxStatus): ContainerStatus
    {
        return match ($proxmoxStatus) {
            'running' => ContainerStatus::Running,
            'paused', 'suspended' => ContainerStatus::Suspended,
            default => ContainerStatus::Stopped,
        };
    }

    /**
     * Build the wss:// URL the browser connects to, pointing directly at Proxmox.
     *
     * @param array<string, mixed> $proxy
     */
    private function buildVncWsUrl(int $vmid, string $node, array $proxy): string
    {
        $parts = parse_url(rtrim((string) config('proxmox.proxy_url'), '/'));
        $wsScheme = ($parts['scheme'] ?? 'http') === 'https' ? 'wss' : 'ws';
        $wsHost = $parts['host'] ?? 'localhost';
        $wsPort = isset($parts['port']) ? ":{$parts['port']}" : '';

        return "{$wsScheme}://{$wsHost}{$wsPort}/api2/json/nodes/{$node}/lxc/{$vmid}/vncwebsocket"
            . '?port=' . ($proxy['port'] ?? '')
            . '&vncticket=' . urlencode((string) ($proxy['ticket'] ?? ''));
    }
}
