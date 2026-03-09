<?php

namespace App\Enums;

enum ContainerStatus: string
{
    case Provisioning = 'provisioning';
    case Running = 'running';
    case Stopped = 'stopped';
    case Suspended = 'suspended';
    case Error = 'error';

    /**
     * Map a raw Proxmox status string to our enum value.
     */
    public static function fromProxmox(string $status): self
    {
        return match ($status) {
            'running' => self::Running,
            'paused', 'suspended' => self::Suspended,
            default => self::Stopped,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Provisioning => 'Provisioning',
            self::Running      => 'Berjalan',
            self::Stopped      => 'Berhenti',
            self::Suspended    => 'Ditangguhkan',
            self::Error        => 'Error',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Provisioning => 'blue',
            self::Running      => 'green',
            self::Stopped      => 'gray',
            self::Suspended    => 'orange',
            self::Error        => 'red',
        };
    }
}
