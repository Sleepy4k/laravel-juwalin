@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'card ' . $class]) }}>
    {{ $slot }}
</div>
