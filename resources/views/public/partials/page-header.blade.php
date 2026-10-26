<section class="relative overflow-hidden bg-forest-700 text-cream">
    <div class="absolute -right-16 -top-24 h-72 w-72 rounded-full bg-harvest-500/30 blur-3xl"></div>
    <div class="relative mx-auto max-w-6xl px-6 py-14">
        <span class="text-xs font-bold uppercase tracking-widest text-harvest-300">{{ $kicker }}</span>
        <h1 class="mt-2 font-display text-3xl font-extrabold sm:text-4xl">{{ $page?->title ?? $fallbackTitle }}</h1>
    </div>
</section>

@if ($page)
    <section class="mx-auto max-w-3xl px-6 pt-10">
        <div class="space-y-4 text-base leading-relaxed text-forest-600">
            @foreach ($page->paragraphs() as $paragraph)
                <p class="whitespace-pre-line">{{ $paragraph }}</p>
            @endforeach
        </div>
    </section>
@endif
