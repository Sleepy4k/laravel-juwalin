<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderContainerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $maxCores = (int) config('proxmox.max_cores_per_container', 4);
        $maxMemory = (int) config('proxmox.max_memory_mb_per_container', 4096);
        $maxDisk = (int) config('proxmox.max_disk_gb_per_container', 50);

        return [
            'cores'     => ['required', 'integer', 'min:1', "max:{$maxCores}"],
            'memory_mb' => ['required', 'integer', 'min:256', "max:{$maxMemory}"],
            'disk_gb'   => ['required', 'integer', 'min:5', "max:{$maxDisk}"],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cores.max'     => 'Maximum :max vCPU cores allowed per container.',
            'memory_mb.max' => 'Maximum :max MB RAM allowed per container.',
            'disk_gb.max'   => 'Maximum :max GB disk allowed per container.',
        ];
    }
}
