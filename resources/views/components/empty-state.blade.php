@props(['icon' => '🧺', 'title' => 'Belum ada data'])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-forest-200 bg-white/60 px-6 py-12 text-center']) }}>
    <div class="text-4xl" aria-hidden="true">{{ $icon }}</div>
    <p class="mt-2 font-display font-bold text-forest-700">{{ $title }}</p>
    @if ($slot->isNotEmpty())
        <div class="mt-1 text-sm text-forest-500">{{ $slot }}</div>
    @endif
</div>
