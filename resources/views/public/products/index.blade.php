@extends('layouts.public')

@section('title', $activeCategory ? 'Kategori '.$activeCategory->name : 'Katalog Produk')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="mx-auto max-w-xl text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-harvest-600">Katalog Produk</span>
            <h1 class="mt-2 font-display text-3xl font-extrabold text-forest-700 sm:text-4xl">
                {{ $activeCategory ? $activeCategory->icon.' '.$activeCategory->name : 'Semua Produk Desa' }}
            </h1>
            <p class="mt-3 text-sm text-forest-500">{{ $activeCategory?->description ?? 'Produk pertanian, perikanan, dan UMKM dari warga Desa Linonggasai yang telah diperiksa pengelola.' }}</p>
        </div>

        <form method="GET" action="{{ $activeCategory ? route('categories.show', $activeCategory) : route('products.index') }}" class="mx-auto mt-8 flex max-w-2xl flex-col gap-2 sm:flex-row">
            <label class="flex flex-1 items-center rounded-full border border-forest-100 bg-white px-4 py-2.5">
                <svg class="h-4 w-4 text-forest-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                <span class="sr-only">Cari produk</span>
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari beras, ikan, kerupuk…" class="ml-2 w-full border-0 bg-transparent p-0 text-sm placeholder:text-forest-400 focus:ring-0">
            </label>
            <label class="sr-only" for="urut">Urutkan</label>
            <select id="urut" name="urut" class="rounded-full border border-forest-100 bg-white px-4 py-2.5 text-sm font-semibold text-forest-600 focus:border-harvest-300 focus:ring-harvest-300">
                @foreach (['terbaru' => 'Terbaru', 'termurah' => 'Harga termurah', 'termahal' => 'Harga termahal', 'nama' => 'Nama A–Z'] as $value => $label)
                    <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn-dark">Cari</button>
        </form>

        <div class="no-scrollbar -mx-4 mt-6 flex gap-2 overflow-x-auto px-4 sm:mx-0 sm:flex-wrap sm:justify-center sm:px-0">
            <a href="{{ route('products.index', array_filter(['q' => $search])) }}" class="chip {{ $activeCategory ? '' : 'chip-active' }}">Semua</a>
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', [$category, ...array_filter(['q' => $search])]) }}" class="chip {{ $activeCategory?->is($category) ? 'chip-active' : '' }}">{{ $category->icon }} {{ $category->name }}</a>
            @endforeach
        </div>

        @if ($products->isEmpty())
            <x-empty-state class="mt-10" icon="🔍" title="Produk tidak ditemukan">
                Coba kata kunci lain atau pilih kategori berbeda.
            </x-empty-state>
        @else
            <p class="mt-8 text-sm text-forest-500">Menampilkan {{ $products->total() }} produk{{ $search !== '' ? ' untuk "'.$search.'"' : '' }}.</p>
            <div class="mt-4 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            {{ $products->links() }}
        @endif
    </section>
@endsection
