@extends('layouts.seller')

@section('title', 'Produk Saya')

@use('App\Enums\ProductStatus')
@use('App\Enums\StockStatus')

@section('content')
    <div class="flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-2xl font-extrabold">Produk Saya</h1>
            <p class="text-sm text-forest-500">Produk tampil di katalog setelah disetujui admin L-DCH.</p>
        </div>
        <a href="{{ route('seller.products.create') }}" class="btn-primary hidden sm:inline-flex">+ Tambah</a>
    </div>

    <form method="GET" class="mt-4 flex items-center rounded-full border border-forest-100 bg-white px-4 py-2.5">
        @if ($activeStatus)
            <input type="hidden" name="status" value="{{ $activeStatus->value }}">
        @endif
        <svg class="h-4 w-4 text-forest-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <label for="q" class="sr-only">Cari produk</label>
        <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Cari produk saya…" class="ml-2 w-full border-0 bg-transparent p-0 text-sm placeholder:text-forest-400 focus:ring-0">
    </form>

    <div class="no-scrollbar -mx-4 mt-4 flex gap-2 overflow-x-auto px-4">
        <a href="{{ route('seller.products.index') }}" class="chip {{ $activeStatus ? '' : 'chip-active' }}">Semua ({{ $statusCounts->sum() }})</a>
        @foreach (ProductStatus::cases() as $status)
            <a href="{{ route('seller.products.index', ['status' => $status->value]) }}" class="chip {{ $activeStatus === $status ? 'chip-active' : '' }}">{{ $status->label() }} ({{ $statusCounts[$status->value] ?? 0 }})</a>
        @endforeach
    </div>

    @if ($products->isEmpty())
        <x-empty-state class="mt-6" icon="🧺" title="Belum ada produk di sini">
            <a href="{{ route('seller.products.create') }}" class="font-bold text-harvest-600">+ Tambahkan produk pertama Anda</a>
        </x-empty-state>
    @else
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            @foreach ($products as $product)
                <div class="card overflow-hidden">
                    <div class="flex">
                        <x-product-thumb :product="$product" class="w-24 shrink-0" icon-size="text-4xl" />
                        <div class="min-w-0 flex-1 p-4">
                            <div class="flex items-start justify-between gap-2">
                                <h2 class="truncate font-display font-bold">{{ $product->name }}</h2>
                                <x-badge :status="$product->status" class="!px-2 !py-0.5 !text-[10px]" />
                            </div>
                            <div class="mt-1 text-sm font-bold text-harvest-600">{{ $product->formattedPrice() }} / {{ $product->unit }}</div>
                            <div class="mt-0.5 text-[11px] text-forest-500">{{ $product->category->icon }} {{ $product->category->name }} · diubah {{ $product->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    @if ($product->status === ProductStatus::Rejected && $product->moderation_note)
                        <div class="border-t border-forest-100 bg-clay-50 px-4 py-2 text-xs text-clay-600"><strong>Alasan ditolak:</strong> {{ $product->moderation_note }}</div>
                    @endif

                    <form method="POST" action="{{ route('seller.products.stock', $product) }}" class="flex flex-wrap items-center gap-2 border-t border-forest-100 px-4 py-2.5">
                        @csrf @method('PATCH')
                        <label for="stock-{{ $product->id }}" class="text-xs font-semibold {{ $product->stock !== null && $product->stock < 10 ? 'text-clay-600' : 'text-forest-500' }}">Stok</label>
                        <input id="stock-{{ $product->id }}" name="stock" type="number" min="0" inputmode="numeric" value="{{ $product->stock }}" placeholder="—" class="w-20 rounded-lg border-forest-200 py-1 text-center text-sm font-bold focus:border-harvest-300 focus:ring-harvest-300">
                        <label for="stock-status-{{ $product->id }}" class="sr-only">Status ketersediaan</label>
                        <select id="stock-status-{{ $product->id }}" name="stock_status" class="rounded-lg border-forest-200 py-1 text-xs font-semibold focus:border-harvest-300 focus:ring-harvest-300">
                            @foreach (StockStatus::cases() as $stockStatus)
                                <option value="{{ $stockStatus->value }}" @selected($product->stock_status === $stockStatus)>{{ $stockStatus->label() }}</option>
                            @endforeach
                        </select>
                        <button class="ml-auto rounded-full bg-forest-700 px-3 py-1.5 text-xs font-bold text-cream">Simpan</button>
                    </form>

                    <div class="flex items-center justify-end gap-3 border-t border-forest-100 px-4 py-2 text-xs font-bold">
                        @if ($product->status === ProductStatus::Published)
                            <a href="{{ route('products.show', $product) }}" target="_blank" class="text-forest-500 hover:text-forest-700">Lihat ↗</a>
                        @endif
                        <a href="{{ route('seller.products.edit', $product) }}" class="text-harvest-600 hover:text-harvest-700">Edit</a>
                        @if ($product->status !== ProductStatus::Archived)
                            <form method="POST" action="{{ route('seller.products.archive', $product) }}" data-confirm="Arsipkan {{ $product->name }}? Produk tidak akan tampil di katalog.">
                                @csrf @method('PATCH')
                                <button class="text-forest-500 hover:text-forest-700">Arsipkan</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('seller.products.destroy', $product) }}" data-confirm="Hapus {{ $product->name }}?">
                            @csrf @method('DELETE')
                            <button class="text-clay-600 hover:text-clay-500">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $products->links() }}
    @endif
@endsection
