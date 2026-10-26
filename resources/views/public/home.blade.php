@extends('layouts.public')

@section('content')
    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden">
        <div class="absolute -right-24 -top-24 h-96 w-96 rounded-full bg-harvest-100 opacity-60 blur-3xl"></div>
        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-6 pb-20 pt-16 md:grid-cols-2">
            <div class="reveal">
                <span class="inline-flex items-center gap-2 rounded-full bg-harvest-100 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-harvest-700">{{ $settings['hero_badge'] }}</span>
                <h1 class="mt-5 font-display text-4xl font-extrabold leading-[1.05] text-forest-700 sm:text-5xl">{{ $settings['hero_title'] }}</h1>
                <p class="mt-5 max-w-md text-base text-forest-600 sm:text-lg">{{ $settings['hero_description'] }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="rounded-full bg-harvest-500 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-harvest-500/25 transition hover:bg-harvest-600">Lihat Katalog Produk</a>
                    <a href="{{ route('pages.how-to-order') }}" class="rounded-full border-2 border-forest-700 px-6 py-3.5 text-sm font-bold text-forest-700 transition hover:bg-forest-700 hover:text-white">Cara Pemesanan</a>
                </div>
                <div class="mt-10 flex gap-8 text-forest-700">
                    <div><div class="font-display text-2xl font-extrabold">{{ $stats['categories'] }}</div><div class="text-xs font-semibold text-forest-500">Kategori Produk</div></div>
                    <div><div class="font-display text-2xl font-extrabold">{{ $stats['products'] }}</div><div class="text-xs font-semibold text-forest-500">Produk Tayang</div></div>
                    <div><div class="font-display text-2xl font-extrabold">{{ $stats['stores'] }}</div><div class="text-xs font-semibold text-forest-500">Toko Warga</div></div>
                </div>
            </div>
            <div class="reveal">
                <div class="relative aspect-[4/5] overflow-hidden rounded-[2rem] shadow-2xl shadow-forest-900/20">
                    <img src="{{ asset('images/gazebo-bumdes-meohai.jpg') }}" class="h-full w-full object-cover" alt="Gazebo BUMDes Meohai Desa Linonggasai">
                    <div class="absolute inset-0 bg-gradient-to-t from-forest-900/70 via-forest-900/0 to-transparent"></div>
                    <div class="absolute bottom-5 left-5 right-5 text-white">
                        <div class="text-xs font-bold uppercase tracking-widest text-harvest-300">BUMDes Meohai</div>
                        <div class="font-display text-lg font-bold">Desa Linonggasai, Wonggeduku Barat</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ BANNER ============ --}}
    @if ($banners->isNotEmpty())
        <section class="mx-auto max-w-6xl px-6 pb-12">
            <div class="grid gap-4 {{ $banners->count() > 1 ? 'md:grid-cols-2' : '' }} {{ $banners->count() > 2 ? 'lg:grid-cols-3' : '' }}">
                @foreach ($banners as $banner)
                    @php($tag = $banner->link_url ? 'a' : 'div')
                    <{{ $tag }} @if ($banner->link_url) href="{{ $banner->link_url }}" @endif class="reveal group relative block aspect-[16/9] overflow-hidden rounded-3xl">
                        <img src="{{ $banner->imageUrl() }}" alt="{{ $banner->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-forest-900/80 to-transparent"></div>
                        <div class="absolute bottom-4 left-5 right-5 text-white">
                            <div class="font-display text-lg font-extrabold">{{ $banner->title }}</div>
                            @if ($banner->subtitle)
                                <div class="text-sm text-forest-100">{{ $banner->subtitle }}</div>
                            @endif
                        </div>
                    </{{ $tag }}>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ TENTANG ============ --}}
    <section class="bg-forest-700 text-cream">
        <div class="mx-auto grid max-w-6xl gap-10 px-6 py-16 md:grid-cols-3">
            <div class="reveal">
                <h2 class="mb-3 font-display text-2xl font-extrabold">Apa itu L-DCH?</h2>
                <p class="text-sm leading-relaxed text-forest-100">{{ $about?->paragraphs()[0] ?? 'Linonggasai Digital Creative Hub adalah pusat katalog dan pemasaran digital yang dikelola Karang Taruna Tunas Maju untuk mendukung digitalisasi ekonomi Desa Linonggasai.' }}</p>
                <a href="{{ route('pages.about') }}" class="mt-4 inline-flex text-sm font-bold text-harvest-300 hover:text-white">Selengkapnya →</a>
            </div>
            <div class="reveal grid grid-cols-2 gap-4 md:col-span-2">
                @foreach ([
                    ['📖', 'Katalog Digital', 'Etalase terpusat produk warga desa'],
                    ['✅', 'Penjual Terverifikasi', 'Setiap penjual diperiksa pengelola'],
                    ['💬', 'Pesan via WhatsApp', 'Langsung terhubung ke penjual'],
                    ['🤝', 'Bayar ke Penjual', 'Tanpa perantara dana, disepakati bersama'],
                ] as [$icon, $title, $text])
                    <div class="rounded-2xl bg-forest-600/60 p-5">
                        <div class="mb-2 text-2xl" aria-hidden="true">{{ $icon }}</div>
                        <div class="font-display text-sm font-bold">{{ $title }}</div>
                        <div class="mt-1 text-xs text-forest-200">{{ $text }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ PRODUK ============ --}}
    <section id="produk" class="mx-auto max-w-6xl px-6 py-20">
        <div class="reveal mx-auto max-w-xl text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-harvest-600">Katalog Produk</span>
            <h2 class="mt-2 font-display text-3xl font-extrabold text-forest-700 sm:text-4xl">Produk Unggulan Desa Linonggasai</h2>
            <p class="mt-3 text-sm text-forest-500 sm:text-base">Segar dari sawah, kolam, dan dapur produksi warga — siap dipesan langsung.</p>
        </div>

        <div class="reveal mt-9 flex flex-wrap justify-center gap-2">
            <a href="{{ route('products.index') }}" class="chip chip-active">Semua</a>
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="chip">{{ $category->icon }} {{ $category->name }} <span class="text-forest-400">({{ $category->products_count }})</span></a>
            @endforeach
        </div>

        @if ($products->isEmpty())
            <x-empty-state class="mt-10" icon="🌱" title="Produk segera hadir">
                Penjual sedang menyiapkan produknya. Silakan kembali lagi nanti.
            </x-empty-state>
        @else
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-10 text-center">
                <a href="{{ route('products.index') }}" class="btn-outline">Lihat Semua Produk →</a>
            </div>
        @endif
    </section>

    {{-- ============ CARA PESAN ============ --}}
    <section class="bg-harvest-50">
        <div class="mx-auto max-w-6xl px-6 py-20">
            <div class="reveal mx-auto max-w-xl text-center">
                <span class="text-xs font-bold uppercase tracking-widest text-harvest-600">Alur Pemesanan</span>
                <h2 class="mt-2 font-display text-3xl font-extrabold text-forest-700">Cara Pesan Produk</h2>
            </div>
            @include('public.partials.order-steps')
            <p class="reveal mx-auto mt-8 max-w-2xl rounded-2xl border border-harvest-100 bg-white px-5 py-4 text-center text-xs text-forest-500">
                <strong class="text-forest-700">Catatan:</strong> Website L-DCH tidak memproses pembayaran. Pembayaran dilakukan langsung kepada penjual dan dikonfirmasi manual oleh penjual.
            </p>
        </div>
    </section>

    {{-- ============ LOKASI ============ --}}
    <section class="mx-auto max-w-6xl px-6 py-20">
        <div class="reveal mx-auto max-w-xl text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-harvest-600">Lokasi Desa</span>
            <h2 class="mt-2 font-display text-3xl font-extrabold text-forest-700">Linonggasai, Wonggeduku Barat</h2>
        </div>
        @include('public.partials.location-cards')
    </section>

    {{-- ============ CTA PENJUAL ============ --}}
    <section class="mx-auto max-w-6xl px-6 pb-20">
        <div class="reveal relative overflow-hidden rounded-3xl bg-forest-700 p-8 text-cream sm:p-12">
            <div class="absolute -right-16 -top-20 h-60 w-60 rounded-full bg-harvest-500/30 blur-3xl"></div>
            <div class="relative grid items-center gap-6 md:grid-cols-3">
                <div class="md:col-span-2">
                    <span class="text-xs font-bold uppercase tracking-widest text-harvest-300">Untuk Warga Linonggasai</span>
                    <h2 class="mt-2 font-display text-2xl font-extrabold sm:text-3xl">Punya hasil panen atau produk usaha?</h2>
                    <p class="mt-2 text-sm text-forest-100">Daftarkan diri sebagai penjual. Setelah diverifikasi pengelola, Anda bisa memasarkan produk sendiri di katalog L-DCH.</p>
                </div>
                <div class="md:text-right">
                    <a href="{{ route('seller.register') }}" class="btn-primary px-6 py-3.5">Daftar Jadi Penjual</a>
                </div>
            </div>
        </div>
    </section>
@endsection
