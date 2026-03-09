<?php

namespace App\Jobs;

use App\Contracts\ContainerRepositoryInterface;
use App\Enums\ActivityCategory;
use App\Enums\ContainerStatus;
use App\Enums\OrderStatus;
use App\Mail\ContainerProvisioned;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Services\Proxmox\ProxmoxContainerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

/**
 * Handles the full async provisioning lifecycle for an LXC container:
 *   1. Determine the next free VMID
 *   2. Clone the template via Proxmox API
 *   3. Poll the task until completion
 *   4. Apply resource config (cores, memory, disk)
 *   5. Start the container
 *   6. Update the local Container & Order records
 */
class ProvisionContainerJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** Maximum number of attempts before the job is marked as failed. */
    public int $tries = 3;

    /** Timeout in seconds per attempt. */
    public int $timeout = 300;

    /** Exponential backoff in seconds between retries. */
    public array $backoff = [30, 60, 120];

    public function __construct(
        public readonly Order $order,
        public readonly string $hostname,
        public readonly string $storagePool = 'local-lvm',
        public readonly string $networkBridge = 'vmbr0',
        public readonly string $ipCidr = 'dhcp',
    ) {}

    public function handle(
        ProxmoxContainerService $proxmox,
        ContainerRepositoryInterface $containerRepo,
    ): void {
        $templateVmid = (int) config('proxmox.template_vmid');
        $node = (string) config('proxmox.node');

        // 1. Determine next VMID (cluster suggestion + local DB guard)
        $vmid = max(
            $proxmox->getNextVmid(),
            $containerRepo->nextAvailableVmid(),
        );

        // 2. Create local Container record immediately so the UI can show "provisioning"
        $container = $containerRepo->create([
            'user_id'   => $this->order->user_id,
            'order_id'  => $this->order->id,
            'vmid'      => $vmid,
            'node'      => $node,
            'hostname'  => $this->hostname,
            'cores'     => $this->order->cores,
            'memory_mb' => $this->order->memory_mb,
            'disk_gb'   => $this->order->disk_gb,
            'storage'   => $this->storagePool,
            'status'    => ContainerStatus::Provisioning,
        ]);

        try {
            // 3. Clone the LXC template
            $upid = $proxmox->cloneContainer(
                templateVmid: $templateVmid,
                newVmid: $vmid,
                hostname: $this->hostname,
                options: [
                    'storage'     => $this->storagePool,
                    'description' => "Container for Order #{$this->order->id}",
                ],
                node: $node,
            );

            $containerRepo->update($container, ['provision_task_upid' => $upid]);

            // 4. Poll until clone task completes (max 4 min with 5-second intervals)
            $this->waitForTask($proxmox, $upid, $node, maxSeconds: 240, pollInterval: 5);

            // After the clone task reports OK, Proxmox still holds the container
            // config lock for a brief moment while it finalises the clone.
            // Waiting 3 seconds prevents the "can't lock file pve-config-{vmid}.lock" error.
            sleep(3);

            // 5. Apply resource configuration (cores + memory + network only).
            //    Rootfs must NOT be set here — Proxmox already assigned the cloned
            //    volume and setting rootfs to "storage:size" would try to attach a
            //    non-existent LV, causing "Not a HASH reference" on the Proxmox side.
            $proxmox->configureContainer($vmid, [
                'cores'  => $this->order->cores,
                'memory' => $this->order->memory_mb,
                'net0'   => "name=eth0,bridge={$this->networkBridge},ip={$this->ipCidr},firewall=1",
            ], $node);

            // 6. Resize the rootfs disk to the ordered size via the dedicated resize endpoint.
            //    The resize PUT returns immediately but Proxmox performs it asynchronously,
            //    holding the config lock until it finishes. Attempting to start the container
            //    immediately causes "can't lock file pve-config-{vmid}.lock".
            //    We therefore leave the container stopped — the user starts it manually.
            $proxmox->resizeContainerDisk($vmid, 'rootfs', "{$this->order->disk_gb}G", $node);

            $containerRepo->update($container, [
                'status'         => ContainerStatus::Stopped,
                'provisioned_at' => now(),
            ]);

            $this->order->update(['status' => OrderStatus::Active]);

            // Send email notification to user
            $container->loadMissing('user');
            if ($container->user) {
                Mail::to($container->user->email)->queue(new ContainerProvisioned($container));
            }

            // Activity log
            ActivityLog::record(
                category: ActivityCategory::Container,
                event: 'container_provisioned',
                description: "Container VMID {$vmid} berhasil diprovisioning dan siap dinyalakan untuk order #{$this->order->id}.",
                userId: $this->order->user_id,
                metadata: ['vmid' => $vmid, 'order_id' => $this->order->id, 'container_id' => $container->id],
            );

            Log::info("[Provision] Container VMID {$vmid} provisioned successfully.", [
                'order_id'     => $this->order->id,
                'container_id' => $container->id,
            ]);
        } catch (Throwable $e) {
            $containerRepo->update($container, ['status' => ContainerStatus::Error]);
            $this->order->update(['status' => OrderStatus::ProvisioningFailed]);

            Log::error("[Provision] Failed for Order #{$this->order->id}: {$e->getMessage()}");

            throw $e; // Let the queue retry / mark as failed
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical("[Provision] Job permanently failed for Order #{$this->order->id}.", [
            'exception' => $exception->getMessage(),
        ]);

        $this->order->update(['status' => 'provisioning_failed']);
    }

    /**
     * Poll a Proxmox UPID task until it finishes or the timeout is reached.
     *
     * @throws RuntimeException when the task fails or times out
     */
    private function waitForTask(
        ProxmoxContainerService $proxmox,
        string $upid,
        string $node,
        int $maxSeconds,
        int $pollInterval,
    ): void {
        $deadline = now()->addSeconds($maxSeconds);

        while (now()->lessThan($deadline)) {
            $status = $proxmox->getTaskStatus($upid, $node);

            if (($status['status'] ?? '') === 'stopped') {
                if (($status['exitstatus'] ?? '') !== 'OK') {
                    throw new RuntimeException(
                        "Proxmox task {$upid} failed: " . ($status['exitstatus'] ?? 'unknown'),
                    );
                }

                return;
            }

            sleep($pollInterval);
        }

        throw new RuntimeException("Proxmox task {$upid} timed out after {$maxSeconds}s.");
    }
}
