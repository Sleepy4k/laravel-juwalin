@props(['label', 'value', 'icon' => null, 'color' => 'blue'])

@php
    $colorMap = [
        'blue'   => 'text-brand-400 bg-brand-500/10',
        'green'  => 'text-green-400 bg-green-500/10',
        'yellow' => 'text-yellow-400 bg-yellow-500/10',
        'red'    => 'text-red-400 bg-red-500/10',
    ];
    $iconCls = $colorMap[$color] ?? $colorMap['blue'];
@endphp

<div class="card card-body">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-100">{{ $value }}</p>
        </div>
        @if($icon)
        <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $iconCls }}">
            {!! $icon !!}
        </div>
        @endif
    </div>
</div>
