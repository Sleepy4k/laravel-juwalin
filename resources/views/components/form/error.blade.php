@props(['field'])

@error($field)
    <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
@enderror
