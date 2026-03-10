<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContainerStatus;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Services\Proxmox\ProxmoxConsoleService;
use App\Services\Proxmox\ProxmoxContainerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ContainerController extends Controller
{
    public function __construct(
        private readonly ProxmoxContainerService $proxmox,
        private readonly ProxmoxConsoleService $console,
    ) {}

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
                $live = $this->proxmox->getStatus($container->vmid, $container->node);
                $synced = ContainerStatus::fromProxmox($live['status'] ?? 'stopped');

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
            $live = $this->proxmox->getStatus($container->vmid, $container->node);
            $synced = ContainerStatus::fromProxmox($live['status'] ?? 'stopped');

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
     * GET /admin/containers/{container}/console
     * Renders the xterm.js console page. Status is validated lazily by the
     * browser via the term-url JSON endpoint so the page itself never 503s.
     */
    public function console(Container $container): View
    {
        return view('admin.containers.console', compact('container'));
    }

    /**
     * GET /admin/containers/{container}/term-url  (JSON).
     *
     * Creates a terminal proxy session server-side and returns a {wsUrl, ticket}
     * pair. wsUrl points to PROXMOX_WS_PROXY_URL, which must forward WebSocket
     * connections to Proxmox from the same IP that created the vncticket.
     */
    public function termUrl(Container $container): JsonResponse
    {
        try {
            $live = $this->proxmox->getStatus($container->vmid, $container->node);
            abort_if(($live['status'] ?? '') !== 'running', 422, 'Container sedang tidak berjalan.');
        } catch (Throwable $e) {
            abort(503, 'Tidak dapat terhubung ke Proxmox: ' . $e->getMessage());
        }

        try {
            $termProxy = $this->console->getTermProxy($container->vmid, $container->node);
        } catch (Throwable $e) {
            abort(503, 'Gagal membuat sesi terminal: ' . $e->getMessage());
        }

        return response()->json([
            'wsUrl'    => $this->console->buildConsoleWsUrl($container->vmid, $container->node, $termProxy),
            'ticket'   => $termProxy['ticket'] ?? '',
            'username' => $termProxy['username'] ?? '',
        ]);
    }

    public function action(Request $request, Container $container): RedirectResponse
    {
        $request->validate(['action' => ['required', 'in:start,stop,shutdown,restart']]);

        $node = $container->node ?? config('proxmox.node');

        // Guard actions against live Proxmox state to prevent spurious errors.
        try {
            $live = $this->proxmox->getStatus($container->vmid, $node);
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
}
