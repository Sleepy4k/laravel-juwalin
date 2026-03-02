@props(['type' => 'info', 'dismissible' => false])

@php
    $cls = match($type) {
        'success' => 'alert-success',
        'error'   => 'alert-error',
        'warning' => 'alert-warning',
        default   => 'alert-info',
    };
@endphp

<div role="alert" {{ $attributes->merge(['class' => $cls]) }}>
    {{ $slot }}
    @if($dismissible)
        <button data-flash-dismiss class="ml-auto shrink-0 opacity-70 hover:opacity-100">&times;</button>
    @endif
</div>
