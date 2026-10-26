@props(['product', 'iconSize' => 'text-5xl'])

{{-- Product photo, or the category gradient + icon placeholder from the reference design. --}}
<div {{ $attributes->merge(['class' => 'relative overflow-hidden bg-gradient-to-br '.$product->category?->gradientClasses()]) }}>
    @if ($product->mainImageUrl())
        <img src="{{ $product->mainImageUrl() }}" alt="{{ $product->name }}" loading="lazy" class="h-full w-full object-cover">
    @else
        <div class="grid h-full w-full place-items-center {{ $iconSize }}" aria-hidden="true">{{ $product->category?->icon ?? '📦' }}</div>
    @endif
</div>
