@props(['for' => null, 'required' => false])

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->merge(['class' => 'label']) }}
>
    {{ $slot }}
    @if($required)
        <span class="text-red-400 ml-0.5">*</span>
    @endif
</label>
