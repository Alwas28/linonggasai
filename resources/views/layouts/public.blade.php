<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', 'Katalog digital produk pertanian, perikanan, dan UMKM Desa Linonggasai. Pesan langsung ke penjual melalui WhatsApp.')">
    <title>@hasSection('title')@yield('title') — @endif{{ $settings['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-forest-700">

@php
    $navLinks = [
        ['label' => 'Tentang', 'route' => 'pages.about', 'active' => ['pages.about']],
        ['label' => 'Produk', 'route' => 'products.index', 'active' => ['products.*', 'categories.*', 'stores.*']],
        ['label' => 'Cara Pesan', 'route' => 'pages.how-to-order', 'active' => ['pages.how-to-order']],
        ['label' => 'Lokasi', 'route' => 'pages.location', 'active' => ['pages.location']],
        ['label' => 'Kontak', 'route' => 'pages.contact', 'active' => ['pages.contact']],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-forest-100 bg-cream/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <x-logo :subtitle="$settings['site_tagline']" />

        <nav class="hidden items-center gap-7 text-sm font-semibold text-forest-600 md:flex" aria-label="Navigasi utama">
            @foreach ($navLinks as $link)
                <a href="{{ route($link['route']) }}" class="transition hover:text-harvest-600 {{ request()->routeIs(...$link['active']) ? 'text-harvest-600' : '' }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('seller.dashboard') }}" class="hidden rounded-full bg-forest-700 px-5 py-2.5 text-sm font-bold text-cream transition hover:bg-forest-600 sm:inline-flex">
                    {{ auth()->user()->isAdmin() ? 'Panel Admin' : 'Toko Saya' }}
                </a>
            @else
                <a href="{{ route('login') }}" class="hidden text-sm font-bold text-forest-600 hover:text-harvest-600 sm:inline">Masuk</a>
                <a href="{{ route('seller.register') }}" class="hidden rounded-full bg-forest-700 px-5 py-2.5 text-sm font-bold text-cream transition hover:bg-forest-600 sm:inline-flex">Jadi Penjual</a>
            @endauth
            <button type="button" data-toggle="#mobileNav" aria-expanded="false" aria-controls="mobileNav" class="rounded-lg border border-forest-300 px-3 py-1.5 text-xs font-bold text-forest-700 md:hidden">MENU</button>
        </div>
    </div>
    <nav id="mobileNav" class="hidden border-t border-forest-100 bg-cream px-6 pb-4 text-sm font-semibold text-forest-600 md:hidden">
        @foreach ($navLinks as $link)
            <a href="{{ route($link['route']) }}" class="block py-2">{{ $link['label'] }}</a>
        @endforeach
        <div class="mt-2 flex gap-2 border-t border-forest-100 pt-3">
            @auth
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('seller.dashboard') }}" class="btn-dark flex-1">{{ auth()->user()->isAdmin() ? 'Panel Admin' : 'Toko Saya' }}</a>
            @else
                <a href="{{ route('login') }}" class="btn-outline flex-1">Masuk</a>
                <a href="{{ route('seller.register') }}" class="btn-dark flex-1">Jadi Penjual</a>
            @endauth
        </div>
    </nav>
</header>

<main>
    @yield('content')
</main>

<footer id="kontak" class="bg-forest-700 text-cream">
    <div class="mx-auto grid max-w-6xl gap-10 px-6 py-14 md:grid-cols-4">
        <div class="md:col-span-2">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-bumdes-meohai.png') }}" class="h-10 w-10 rounded-full bg-white p-0.5" alt="Logo BUMDes Meohai">
                <span class="font-display font-extrabold">L-DCH LINONGGASAI</span>
            </div>
            <p class="mt-4 max-w-sm text-sm text-forest-200">Pusat katalog dan pemasaran digital produk pertanian, perikanan, dan UMKM Desa Linonggasai. Transaksi dilakukan langsung antara pembeli dan penjual melalui WhatsApp.</p>
        </div>
        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-harvest-300">Kategori</h5>
            <ul class="space-y-2 text-sm text-forest-200">
                @foreach ($navCategories as $category)
                    <li><a href="{{ route('categories.show', $category) }}" class="hover:text-white">{{ $category->icon }} {{ $category->name }}</a></li>
                @endforeach
                <li><a href="{{ route('seller.register') }}" class="hover:text-white">Daftar sebagai penjual</a></li>
            </ul>
        </div>
        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-harvest-300">Hubungi Kami</h5>
            <ul class="space-y-2 text-sm text-forest-200">
                <li>{{ $settings['address'] }}</li>
                <li>{{ $settings['manager_name'] }}</li>
                @if ($settings['admin_whatsapp'])
                    <li><a href="{{ \App\Services\WhatsApp::link($settings['admin_whatsapp']) }}" target="_blank" rel="noopener" class="font-bold text-harvest-300 hover:text-white">WhatsApp {{ \App\Services\WhatsApp::display($settings['admin_whatsapp']) }}</a></li>
                @endif
                @if ($settings['contact_email'])
                    <li>{{ $settings['contact_email'] }}</li>
                @endif
            </ul>
        </div>
    </div>
    <div class="border-t border-forest-600 py-5">
        <div class="mx-auto flex max-w-6xl flex-wrap justify-between gap-2 px-6 text-xs text-forest-300">
            <span>© {{ now()->year }} L-DCH · Desa Linonggasai</span>
            <span>Dikelola oleh {{ $settings['manager_name'] }} · BUMDes Meohai</span>
        </div>
    </div>
</footer>

<x-flash />
</body>
</html>
