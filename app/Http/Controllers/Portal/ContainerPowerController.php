<?php

namespace App\Http\Controllers\Portal;

use App\Contracts\ContainerRepositoryInterface;
use App\Enums\ContainerStatus;
use App\Http\Controllers\Controller;
use App\Services\Proxmox\ProxmoxContainerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class ContainerPowerController extends Controller
{
    public function __construct(
        private readonly ContainerRepositoryInterface $containers,
        private readonly ProxmoxContainerService $proxmox,
    ) {}

    /**
     * POST /portal/containers/{container}/start.
     */
    public function start(Request $request, int $id): RedirectResponse
    {
        $container = $this->containers->findById($id);

        abort_if($container === null || $container->user_id !== $request->user()->id, 404);

        try {
            $live = $this->proxmox->getStatus($container->vmid, $container->node);
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
            $live = $this->proxmox->getStatus($container->vmid, $container->node);
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
            $live = $this->proxmox->getStatus($container->vmid, $container->node);
            abort_if(($live['status'] ?? '') !== 'running', 422, 'Container tidak sedang berjalan.');
        } catch (Throwable) {
        }

        $this->proxmox->rebootContainer($container->vmid, $container->node);

        return back()->with('success', 'Permintaan restart container dikirim.');
    }
}
