<?php

namespace App\Http\Controllers\Portal;

use App\Contracts\ContainerRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\Proxmox\ProxmoxConsoleService;
use App\Services\Proxmox\ProxmoxContainerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ContainerConsoleController extends Controller
{
    public function __construct(
        private readonly ContainerRepositoryInterface $containers,
        private readonly ProxmoxContainerService $proxmox,
        private readonly ProxmoxConsoleService $console,
    ) {}

    /**
     * GET /portal/containers/{container}/console
     * Renders the xterm.js console page. Status is validated lazily by the
     * browser via the term-url JSON endpoint so the page itself never 503s.
     */
    public function show(Request $request, int $id): View
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

        return view('portal.containers.console', compact('container'));
    }

    /**
     * GET /portal/containers/{container}/term-url  (JSON).
     *
     * Creates a terminal proxy session server-side and returns a {wsUrl, ticket}
     * pair. wsUrl points to PROXMOX_WS_PROXY_URL, which must forward WebSocket
     * connections to Proxmox from the same IP that created the vncticket.
     */
    public function termUrl(Request $request, int $id): JsonResponse
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

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
            'wsUrl'  => $this->console->buildConsoleWsUrl($container->vmid, $container->node, $termProxy),
            'ticket' => $termProxy['ticket'] ?? '',
        ]);
    }
}
