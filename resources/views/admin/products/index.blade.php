@extends('layouts.admin')

@section('title', 'Moderasi Produk')

@use('App\Enums\ProductStatus')

@section('content')
    <div class="no-scrollbar flex gap-2 overflow-x-auto">
        @foreach (ProductStatus::cases() as $status)
            <a href="{{ route('admin.products.index', ['status' => $status->value]) }}" class="chip {{ $activeStatus === $status ? 'chip-active' : '' }}">{{ $status->label() }} ({{ $statusCounts[$status->value] ?? 0 }})</a>
        @endforeach
        <a href="{{ route('admin.products.index', ['status' => 'semua']) }}" class="chip {{ $activeStatus ? '' : 'chip-active' }}">Semua</a>
    </div>

    <form method="GET" class="mt-4 flex flex-col gap-2 sm:flex-row">
        <input type="hidden" name="status" value="{{ $activeStatus?->value ?? 'semua' }}">
        <label for="q" class="sr-only">Cari produk</label>
        <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Cari nama produk…" class="form-input mt-0 sm:max-w-xs">
        <label for="kategori" class="sr-only">Kategori</label>
        <select id="kategori" name="kategori" class="form-input mt-0 sm:max-w-[12rem]">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected($activeCategory === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button class="btn-dark">Filter</button>
    </form>

    @if ($products->isEmpty())
        <x-empty-state class="mt-5" icon="✅" title="{{ $activeStatus === ProductStatus::Pending ? 'Tidak ada produk menunggu moderasi' : 'Tidak ada produk' }}" />
    @else
        <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <a href="{{ route('admin.products.show', $product) }}" class="card flex overflow-hidden transition hover:border-harvest-300">
                    <x-product-thumb :product="$product" class="w-24 shrink-0" icon-size="text-4xl" />
                    <div class="min-w-0 flex-1 p-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <h3 class="truncate font-display font-bold">{{ $product->name }}</h3>
                                <div class="truncate text-xs text-forest-500">{{ $product->store->name }}</div>
                            </div>
                            <x-badge :status="$product->status" class="!px-2 !py-0.5 !text-[10px]" />
                        </div>
                        <div class="mt-2 text-sm font-bold text-harvest-600">{{ $product->formattedPrice() }} / {{ $product->unit }}</div>
                        <div class="mt-1 text-[11px] font-semibold text-forest-500">{{ $product->category->icon }} {{ $product->category->name }} · {{ $product->updated_at->diffForHumans() }}</div>
                    </div>
                </a>
            @endforeach
        </div>
        {{ $products->links() }}
    @endif
@endsection
