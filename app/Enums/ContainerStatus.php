<?php

namespace App\Enums;

enum ContainerStatus: string
{
    case Provisioning = 'provisioning';
    case Running = 'running';
    case Stopped = 'stopped';
    case Suspended = 'suspended';
    case Error = 'error';

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
