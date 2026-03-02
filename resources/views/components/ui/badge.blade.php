@props(['color' => 'gray'])

@php
    $colorMap = [
        'green'  => 'badge-green',
        'red'    => 'badge-red',
        'yellow' => 'badge-yellow',
        'blue'   => 'badge-blue',
        'gray'   => 'badge-gray',
    ];
    $cls = $colorMap[$color] ?? 'badge-gray';
@endphp

<span {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</span>
