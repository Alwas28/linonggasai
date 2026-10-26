<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Toko Saya') — {{ $settings['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-forest-700">

@php
    $approved = auth()->user()->isApprovedSeller();
    $topLinks = array_filter([
        ['label' => 'Beranda', 'route' => 'seller.dashboard', 'active' => 'seller.dashboard'],
        $approved ? ['label' => 'Produk Saya', 'route' => 'seller.products.index', 'active' => 'seller.products.*'] : null,
        $approved ? ['label' => 'Pesanan', 'route' => 'seller.orders.index', 'active' => 'seller.orders.*'] : null,
        $approved ? ['label' => 'Laporan', 'route' => 'seller.reports.sales', 'active' => 'seller.reports.*'] : null,
        $approved ? ['label' => 'Profil Toko', 'route' => 'seller.store.edit', 'active' => 'seller.store.*'] : null,
        ['label' => 'Akun', 'route' => 'seller.account', 'active' => ['seller.account*', 'seller.settings']],
    ]);
@endphp

<header class="sticky top-0 z-40 border-b border-forest-100 bg-cream/90 backdrop-blur">
    <div class="mx-auto flex max-w-5xl items-center gap-3 px-4 py-3 sm:px-6">
        <x-logo subtitle="Toko Warga" :href="route('seller.dashboard')" />
        <nav class="ml-6 hidden items-center gap-1 text-sm font-semibold text-forest-600 md:flex" aria-label="Menu penjual">
            @foreach ($topLinks as $link)
                <a href="{{ route($link['route']) }}" class="rounded-full px-4 py-2 transition {{ request()->routeIs(...(array) $link['active']) ? 'bg-forest-700 text-cream' : 'hover:bg-forest-50' }}">{{ $link['label'] }}</a>
            @endforeach
        </nav>
        <div class="ml-auto flex items-center gap-2">
            @if ($approved)
                <a href="{{ route('seller.products.create') }}" class="btn-primary hidden md:inline-flex">+ Jual Produk</a>
            @endif
            <a href="{{ route('home') }}" class="grid h-10 w-10 place-items-center rounded-full border border-forest-100 bg-white" title="Lihat katalog publik" aria-label="Lihat katalog publik">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/></svg>
            </a>
        </div>
    </div>
</header>

<main class="mx-auto max-w-5xl px-4 py-5 pb-32 sm:px-6 md:pb-12">
    @yield('content')
</main>

{{-- Bottom nav (mobile) --}}
<nav class="pb-safe fixed inset-x-0 bottom-0 z-50 border-t border-forest-100 bg-white/95 backdrop-blur md:hidden" aria-label="Menu penjual">
    <div class="grid {{ $approved ? 'grid-cols-5' : 'grid-cols-2' }} items-end text-[11px] font-semibold text-forest-500">
        @php($bottomClass = fn ($pattern) => request()->routeIs(...(array) $pattern) ? 'text-harvest-600' : '')
        <a href="{{ route('seller.dashboard') }}" class="flex flex-col items-center gap-1 py-2.5 {{ $bottomClass('seller.dashboard') }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12l9-8 9 8M5 10v10h5v-6h4v6h5V10"/></svg>Beranda
        </a>
        @if ($approved)
            <a href="{{ route('seller.products.index') }}" class="flex flex-col items-center gap-1 py-2.5 {{ $bottomClass(['seller.products.index', 'seller.products.edit']) }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 10L4 17V7m8 14V11"/></svg>Produk
            </a>
            <div class="flex flex-col items-center pb-2">
                <a href="{{ route('seller.products.create') }}" class="-mt-7 grid h-14 w-14 place-items-center rounded-full bg-harvest-500 text-3xl leading-none text-white shadow-xl shadow-harvest-500/40 ring-4 ring-cream transition active:scale-95" aria-label="Jual produk">+</a>
                <span class="mt-1">Jual</span>
            </div>
            <a href="{{ route('seller.orders.index') }}" class="flex flex-col items-center gap-1 py-2.5 {{ $bottomClass('seller.orders.*') }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>Pesanan
            </a>
        @endif
        <a href="{{ route('seller.account') }}" class="flex flex-col items-center gap-1 py-2.5 {{ $bottomClass(['seller.account*', 'seller.settings', 'seller.store.*']) }}">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Profil
        </a>
    </div>
</nav>

<x-flash />
</body>
</html>
