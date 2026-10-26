@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="mt-8 flex flex-wrap items-center justify-center gap-2 text-sm font-bold">
        @if ($paginator->onFirstPage())
            <span class="rounded-full border border-forest-100 px-4 py-2 text-forest-300">‹ Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-full border border-forest-200 bg-white px-4 py-2 text-forest-600 hover:bg-forest-50">‹ Sebelumnya</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-forest-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="grid h-9 min-w-9 place-items-center rounded-full bg-forest-700 px-3 text-cream">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="hidden h-9 min-w-9 place-items-center rounded-full border border-forest-200 bg-white px-3 text-forest-600 hover:bg-forest-50 sm:grid">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-full border border-forest-200 bg-white px-4 py-2 text-forest-600 hover:bg-forest-50">Berikutnya ›</a>
        @else
            <span class="rounded-full border border-forest-100 px-4 py-2 text-forest-300">Berikutnya ›</span>
        @endif
    </nav>
@endif
