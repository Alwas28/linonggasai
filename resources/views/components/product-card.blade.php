@props(['product'])

<article class="card-hover flex flex-col overflow-hidden rounded-2xl border border-forest-100 bg-white">
    <a href="{{ route('products.show', $product) }}" class="block">
        <x-product-thumb :product="$product" class="h-44" icon-size="text-6xl" />
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center justify-between gap-2">
            <span class="tag rounded-l-sm rounded-r-md px-3 py-1 text-[11px] font-bold uppercase tracking-wide {{ $product->category->tagClasses() }}">{{ $product->category->name }}</span>
            <span class="text-[11px] font-semibold {{ $product->stock === 0 || $product->stock_status->value === 'out_of_stock' ? 'text-clay-600' : 'text-forest-400' }}">{{ $product->stockLabel() }}</span>
        </div>
        <h3 class="mt-3 font-display text-lg font-bold text-forest-700">
            <a href="{{ route('products.show', $product) }}" class="hover:text-harvest-600">{{ $product->name }}</a>
        </h3>
        <div class="mt-1 text-sm font-bold text-harvest-600">{{ $product->formattedPrice() }} <span class="font-semibold text-forest-500">/ {{ $product->unit }}</span></div>
        <p class="mt-1 line-clamp-2 text-xs text-forest-500">{{ $product->description }}</p>
        <div class="mt-auto flex items-center justify-between gap-2 pt-4">
            <a href="{{ route('stores.show', $product->store) }}" class="truncate text-xs font-semibold text-forest-500 hover:text-forest-700">🏪 {{ $product->store->name }}</a>
            <a href="{{ $product->whatsappUrl() }}" target="_blank" rel="noopener" class="inline-flex shrink-0 items-center gap-1.5 text-sm font-bold text-harvest-600 hover:text-harvest-700">
                Pesan via WhatsApp <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</article>
