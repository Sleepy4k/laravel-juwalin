<?php

namespace App\Http\Controllers;

use App\Contracts\ContainerRepositoryInterface;
use App\Enums\ContainerStatus;
use App\Services\ProxmoxApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ContainerController extends Controller
{
    public function __construct(
        private readonly ContainerRepositoryInterface $containers,
        private readonly ProxmoxApiService $proxmox,
    ) {}

    /**
     * GET /portal/containers
     * Client portal: list all containers for the authenticated user.
     */
    public function index(Request $request): View
    {
        $containers = $this->containers->paginateForUser(
            userId: $request->user()->id,
            perPage: 10,
        );

        return view('portal.containers.index', compact('containers'));
    }

    /**
     * GET /portal/containers/{container}
     * Show a single container; syncs live Proxmox status into DB on every visit.
     */
    public function show(Request $request, int $id): View
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

        if (!$container->isProvisioning() && $container->vmid) {
            try {
                $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
                $synced = $this->mapProxmoxStatus($live['status'] ?? 'stopped');

                if ($container->status !== $synced) {
                    $this->containers->update($container, ['status' => $synced]);
                    $container->status = $synced;
                }
            } catch (Throwable) {
                // Proxmox unreachable — show stale DB status
            }
        }

        return view('portal.containers.show', compact('container'));
    }

    /**
     * GET /portal/containers/{container}/status  (JSON, polled by JS)
     * Returns live Proxmox status; syncs to DB on change.
     */
    public function status(Request $request, int $id): JsonResponse
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

        if ($container->isProvisioning() || !$container->vmid) {
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
                $this->containers->update($container, ['status' => $synced]);
                $container->status = $synced;
            }
        } catch (Throwable) {
            // Return stale status when Proxmox is unreachable
        }

        return response()->json([
            'status' => $container->status->value,
            'label'  => $container->status->label(),
            'color'  => $container->status->color(),
        ]);
    }

    /**
     * GET /portal/containers/{container}/console
     * Opens a noVNC console session in the browser.
     */
    public function console(Request $request, int $id): View
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

        // Always verify live state — DB may be stale
        try {
            $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
            abort_if(($live['status'] ?? '') !== 'running', 422, 'Container sedang tidak berjalan.');
        } catch (Throwable $e) {
            abort(503, 'Tidak dapat terhubung ke Proxmox: ' . $e->getMessage());
        }

        return view('portal.containers.console', compact('container'));
    }

    /**
     * GET /portal/containers/{container}/vnc-url
     * Returns a fresh wss:// URL with a new VNC ticket as JSON.
     * Called by the console view after noVNC loads to avoid ticket expiry.
     */
    public function vncUrl(Request $request, int $id): JsonResponse
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

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
     * GET /portal/containers/{container}/term-url
     * Creates a termproxy session and returns a fresh wss:// URL + ticket.
     * The ticket must be sent as the first WebSocket message to authenticate.
     */
    public function termUrl(Request $request, int $id): JsonResponse
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

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

    /**
     * POST /portal/containers/{container}/start.
     */
    public function start(Request $request, int $id): RedirectResponse
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

        // Gate against live Proxmox state to avoid spurious errors
        try {
            $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
            abort_if(($live['status'] ?? '') === 'running', 422, 'Container sudah berjalan.');
        } catch (Throwable) {
        }

        $this->proxmox->startContainer($container->vmid, $container->node);
        $this->containers->update($container, ['status' => ContainerStatus::Running]);

        return back()->with('success', 'Container sedang dinyalakan.');
    }

    /**
     * POST /portal/containers/{container}/stop.
     */
    public function stop(Request $request, int $id): RedirectResponse
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

        try {
            $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
            abort_if(($live['status'] ?? '') !== 'running', 422, 'Container tidak sedang berjalan.');
        } catch (Throwable) {
        }

        $this->proxmox->shutdownContainer($container->vmid, 60, $container->node);
        $this->containers->update($container, ['status' => ContainerStatus::Stopped]);

        return back()->with('success', 'Permintaan shutdown container dikirim.');
    }

    /**
     * POST /portal/containers/{container}/restart.
     */
    public function restart(Request $request, int $id): RedirectResponse
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

        try {
            $live = $this->proxmox->getContainerStatus($container->vmid, $container->node);
            abort_if(($live['status'] ?? '') !== 'running', 422, 'Container tidak sedang berjalan.');
        } catch (Throwable) {
        }

        $this->proxmox->rebootContainer($container->vmid, $container->node);

        return back()->with('success', 'Permintaan restart container dikirim.');
    }

    /**
     * GET /api/proxmox/resources
     * JSON endpoint polled by the order form resource sliders.
     * Returns remaining node capacity so the JS can guard the sliders.
     */
    public function availableResources(): JsonResponse
    {
        $resources = $this->proxmox->getAvailableResources();

        return response()->json([
            'free_memory_mb' => $resources['free_memory_mb'],
            'free_disk_gb'   => $resources['free_disk_gb'],
            'max_cores'      => config('proxmox.max_cores_per_container'),
            'max_memory_mb'  => min(
                config('proxmox.max_memory_mb_per_container'),
                $resources['free_memory_mb'],
            ),
            'max_disk_gb' => min(
                config('proxmox.max_disk_gb_per_container'),
                (int) $resources['free_disk_gb'],
            ),
        ]);
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
