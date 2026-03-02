<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortForwardingRequest extends FormRequest
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
        return [
            'container_id'     => ['required', 'integer', 'exists:containers,id'],
            'protocol'         => ['required', 'in:tcp,udp'],
            'source_port'      => ['required', 'integer', 'min:1024', 'max:65535'],
            'destination_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'reason'           => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'source_port.min' => 'Public ports below 1024 are reserved.',
        ];
    }
}
