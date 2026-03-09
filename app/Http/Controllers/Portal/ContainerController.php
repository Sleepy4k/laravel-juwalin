<?php

namespace App\Http\Controllers\Portal;

use App\Contracts\ContainerRepositoryInterface;
use App\Enums\ContainerStatus;
use App\Http\Controllers\Controller;
use App\Services\Proxmox\ProxmoxContainerService;
use App\Services\Proxmox\ProxmoxNodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ContainerController extends Controller
{
    public function __construct(
        private readonly ContainerRepositoryInterface $containers,
        private readonly ProxmoxContainerService $proxmox,
        private readonly ProxmoxNodeService $node,
    ) {}

    /**
     * GET /portal/containers
     * List all containers owned by the authenticated user.
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
                $live = $this->proxmox->getStatus($container->vmid, $container->node);
                $synced = ContainerStatus::fromProxmox($live['status'] ?? 'stopped');

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
     * Returns live status; syncs to DB on change.
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
            $live = $this->proxmox->getStatus($container->vmid, $container->node);
            $synced = ContainerStatus::fromProxmox($live['status'] ?? 'stopped');

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
     * GET /portal/containers/resources  (JSON, polled by order form sliders)
     * Returns available node capacity so the JS can cap resource sliders.
     */
    public function availableResources(): JsonResponse
    {
        $resources = $this->node->getAvailableResources();

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
}
