@props(['label', 'value', 'note' => null, 'noteClass' => 'text-forest-400', 'href' => null])

@php($tag = $href ? 'a' : 'div')
<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->merge(['class' => 'block rounded-2xl border border-forest-100 bg-white p-4 transition '.($href ? 'hover:border-harvest-300' : '')]) }}>
    <div class="text-xs font-semibold text-forest-500">{{ $label }}</div>
    <div class="mt-1 font-display text-xl font-extrabold sm:text-2xl">{{ $value }}</div>
    @if ($note)
        <div class="mt-1 text-[11px] font-bold {{ $noteClass }}">{{ $note }}</div>
    @endif
</{{ $tag }}>
