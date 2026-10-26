@extends('layouts.public')

@section('title', $product->name)
@section('description', \Illuminate\Support\Str::limit($product->description, 150))

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12">
        <nav class="text-xs font-semibold text-forest-500" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-harvest-600">Beranda</a> ›
            <a href="{{ route('products.index') }}" class="hover:text-harvest-600">Produk</a> ›
            <a href="{{ route('categories.show', $product->category) }}" class="hover:text-harvest-600">{{ $product->category->name }}</a> ›
            <span class="text-forest-700">{{ $product->name }}</span>
        </nav>

        <div class="mt-6 grid gap-8 lg:grid-cols-2">
            {{-- Photos --}}
            <div>
                <x-product-thumb :product="$product" class="aspect-square rounded-3xl" icon-size="text-8xl" />
                @if ($product->images->isNotEmpty())
                    <div class="mt-3 grid grid-cols-4 gap-3">
                        @foreach ($product->images as $image)
                            <a href="{{ $image->url() }}" target="_blank" rel="noopener" class="block aspect-square overflow-hidden rounded-xl border border-forest-100">
                                <img src="{{ $image->url() }}" alt="{{ $image->alt_text ?? $product->name }}" loading="lazy" class="h-full w-full object-cover transition hover:scale-105">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Detail --}}
            <div>
                <span class="tag rounded-l-sm rounded-r-md px-3 py-1 text-[11px] font-bold uppercase tracking-wide {{ $product->category->tagClasses() }}">{{ $product->category->name }}</span>
                <h1 class="mt-4 font-display text-3xl font-extrabold text-forest-700 sm:text-4xl">{{ $product->name }}</h1>
                <div class="mt-3 font-display text-2xl font-extrabold text-harvest-600">{{ $product->formattedPrice() }} <span class="text-base font-semibold text-forest-500">/ {{ $product->unit }}</span></div>
                <div class="mt-2 text-sm font-semibold {{ $product->stock === 0 ? 'text-clay-600' : 'text-forest-500' }}">{{ $product->stockLabel() }}</div>

                <div class="mt-6 whitespace-pre-line text-sm leading-relaxed text-forest-600">{{ $product->description }}</div>

                {{-- WhatsApp order --}}
                <div id="wa-order" class="mt-8 rounded-2xl border border-forest-100 bg-white p-5"
                     data-base="https://wa.me/{{ \App\Services\WhatsApp::normalize($product->store->whatsapp_number) }}"
                     data-message="{{ $product->whatsappMessage() }}"
                     data-unit="{{ $product->unit }}">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-bold text-forest-600">Jumlah ({{ $product->unit }})</span>
                        <div class="flex items-center gap-1">
                            <button type="button" data-step="-1" class="h-9 w-9 rounded-full bg-forest-50 text-lg font-bold leading-none" aria-label="Kurangi">−</button>
                            <label class="sr-only" for="quantity">Jumlah</label>
                            <input id="quantity" name="quantity" type="number" min="1" value="1" inputmode="numeric" class="w-16 rounded-lg border-forest-200 text-center font-display font-bold focus:border-harvest-300 focus:ring-harvest-300">
                            <button type="button" data-step="1" class="h-9 w-9 rounded-full bg-forest-700 text-lg font-bold leading-none text-cream" aria-label="Tambah">+</button>
                        </div>
                    </div>
                    <a data-wa-link href="{{ $product->whatsappUrl() }}" target="_blank" rel="noopener"
                       class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-[#25D366] py-3.5 text-sm font-bold text-white shadow-lg shadow-green-500/25 transition hover:brightness-95">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.5 14.4c-.3-.1-1.8-.9-2-1s-.5-.1-.7.1-.8 1-1 1.2-.4.2-.7.1a8 8 0 01-2.4-1.5 9 9 0 01-1.6-2c-.2-.3 0-.5.1-.6l.5-.6.3-.5v-.5l-.9-2.2c-.2-.6-.5-.5-.7-.5h-.6a1.1 1.1 0 00-.8.4 3.4 3.4 0 00-1 2.5 5.9 5.9 0 001.2 3.1 13.4 13.4 0 005.2 4.6c1.9.8 2.6.9 3.6.7a3 3 0 002-1.4 2.5 2.5 0 00.2-1.4c-.1-.1-.3-.2-.6-.3zM12 21.8a9.9 9.9 0 01-5-1.4l-.4-.2-3.7 1 1-3.6-.2-.4A9.9 9.9 0 1112 21.8zM20.5 3.5A11.8 11.8 0 001.7 17.7L0 24l6.4-1.7A11.8 11.8 0 0024 12a11.7 11.7 0 00-3.5-8.5z"/></svg>
                        Pesan via WhatsApp
                    </a>
                    <p class="mt-3 text-center text-[11px] text-forest-400">Harga akhir, ongkir, dan cara pembayaran disepakati langsung dengan penjual.</p>
                </div>

                {{-- Seller --}}
                <a href="{{ route('stores.show', $product->store) }}" class="mt-4 flex items-center gap-3 rounded-2xl border border-forest-100 bg-white p-4 transition hover:border-harvest-300">
                    @if ($product->store->logoUrl())
                        <img src="{{ $product->store->logoUrl() }}" alt="{{ $product->store->name }}" class="h-12 w-12 rounded-full object-cover">
                    @else
                        <div class="grid h-12 w-12 place-items-center rounded-full bg-harvest-500 font-display font-extrabold text-white">{{ $product->store->initials() }}</div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-harvest-600">✓ Penjual Terverifikasi</div>
                        <div class="truncate font-display font-bold">{{ $product->store->name }}</div>
                        <div class="truncate text-xs text-forest-500">{{ $product->store->address }}{{ $product->store->business_hours ? ' · '.$product->store->business_hours : '' }}</div>
                    </div>
                    <span class="text-forest-300" aria-hidden="true">›</span>
                </a>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-16">
                <h2 class="font-display text-2xl font-extrabold">Produk {{ $product->category->name }} Lainnya</h2>
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
