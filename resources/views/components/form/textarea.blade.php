@props(['disabled' => false, 'rows' => 4])

<textarea
    rows="{{ $rows }}"
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'input']) !!}
>{{ $slot }}</textarea>
