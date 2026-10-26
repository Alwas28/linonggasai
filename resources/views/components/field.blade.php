@props(['name', 'label', 'hint' => null])

{{-- Label + slot (input) + validation error, styled like the reference forms. --}}
<div {{ $attributes }}>
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    {{ $slot }}
    @if ($hint)
        <p class="mt-1 text-[11px] text-forest-400">{{ $hint }}</p>
    @endif
    @error($name)
        <p class="mt-1 text-xs font-semibold text-clay-600">{{ $message }}</p>
    @enderror
</div>
