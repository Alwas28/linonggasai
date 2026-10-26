<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Dashboard') — Admin {{ $settings['site_name'] }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-forest-700">

@php
    $icons = [
        'home' => 'M3 12l9-8 9 8M5 10v10h5v-6h4v6h5V10',
        'check' => 'M9 12l2 2 4-4m5.6-4A12 12 0 0112 2.9 12 12 0 013.4 6 12 12 0 003 9c0 5.6 3.8 10.3 9 11.6 5.2-1.3 9-6 9-11.6 0-1-.1-2-.4-3z',
        'users' => 'M17 20h5v-2a3 3 0 00-5.4-1.8M17 20H7m10 0v-2c0-.7-.1-1.3-.4-1.8M7 20H2v-2a3 3 0 015.4-1.8M7 20v-2c0-.7.1-1.3.4-1.8m0 0a5 5 0 019.2 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'box' => 'M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 10L4 17V7m8 14V11',
        'tag' => 'M7 7h.01M7 3h5a2 2 0 011.4.6l7 7a2 2 0 010 2.8l-7 7a2 2 0 01-2.8 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z',
        'clipboard' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        'image' => 'M4 16l4.6-4.6a2 2 0 012.8 0L16 16m-2-2l1.6-1.6a2 2 0 012.8 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        'doc' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.6a1 1 0 01.7.3l5.4 5.4a1 1 0 01.3.7V19a2 2 0 01-2 2z',
        'chart' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        'key' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.7 5.7L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.6a1 1 0 01.3-.7l6-6A6 6 0 1121 9z',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'cog' => 'M10.3 4.3c.4-1.8 3-1.8 3.4 0a1.7 1.7 0 002.6 1c1.5-.9 3.3.8 2.4 2.4a1.7 1.7 0 001 2.6c1.8.4 1.8 3 0 3.4a1.7 1.7 0 00-1 2.6c.9 1.5-.9 3.3-2.4 2.4a1.7 1.7 0 00-2.6 1c-.4 1.8-3 1.8-3.4 0a1.7 1.7 0 00-2.6-1c-1.5.9-3.3-.9-2.4-2.4a1.7 1.7 0 00-1-2.6c-1.8-.4-1.8-3 0-3.4a1.7 1.7 0 001-2.6c-.9-1.6.9-3.3 2.4-2.4 1 .6 2.3.1 2.6-1zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
    ];

    $menu = [
        'Utama' => [
            ['Dashboard', 'admin.dashboard', 'admin.dashboard', 'home', null],
            ['Verifikasi Penjual', 'admin.verification.index', 'admin.verification.*', 'check', $queueCounts['sellers']],
            ['Moderasi Produk', 'admin.products.index', 'admin.products.*', 'box', $queueCounts['products']],
            ['Penjual', 'admin.sellers.index', 'admin.sellers.*', 'users', null],
            ['Rekap Pesanan', 'admin.orders.index', 'admin.orders.*', 'clipboard', null],
        ],
        'Konten' => [
            ['Kategori', 'admin.categories.index', 'admin.categories.*', 'tag', null],
            ['Banner', 'admin.banners.index', 'admin.banners.*', 'image', null],
            ['Halaman Informasi', 'admin.pages.index', 'admin.pages.*', 'doc', null],
        ],
        'Sistem' => [
            ['Laporan', 'admin.reports.sales', 'admin.reports*', 'chart', null],
            ['Pengguna', 'admin.users.index', 'admin.users.*', 'key', null],
            ['Log Aktivitas', 'admin.activity', 'admin.activity', 'clock', null],
            ['Pengaturan', 'admin.settings', 'admin.settings*', 'cog', null],
        ],
    ];
    $user = auth()->user();
@endphp

<div class="min-h-screen lg:flex">
    {{-- Sidebar (desktop) --}}
    <aside class="sticky top-0 hidden h-screen w-64 shrink-0 flex-col bg-forest-700 text-cream lg:flex">
        <div class="border-b border-forest-600 px-6 py-5">
            <x-logo subtitle="Panel Admin" :dark="true" :href="route('admin.dashboard')" />
        </div>
        <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-5 text-sm font-semibold text-forest-100" aria-label="Menu admin">
            @foreach ($menu as $group => $items)
                <div>
                    <div class="px-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-forest-300">{{ $group }}</div>
                    @foreach ($items as [$label, $route, $pattern, $icon, $count])
                        @php($active = request()->routeIs($pattern))
                        <a href="{{ route($route) }}" class="flex w-full items-center gap-3 rounded-xl px-4 py-2.5 transition {{ $active ? 'bg-harvest-100 text-forest-700' : 'hover:bg-forest-600' }}">
                            <svg class="h-5 w-5 {{ $active ? 'text-harvest-600' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons[$icon] }}"/></svg>
                            {{ $label }}
                            @if ($count)
                                <span class="ml-auto rounded-full bg-harvest-500 px-2 py-0.5 text-[10px] font-bold text-white">{{ $count }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>
        <div class="m-3 rounded-2xl bg-forest-600/70 p-4">
            <div class="flex items-center gap-3">
                <div class="grid h-9 w-9 place-items-center rounded-full bg-harvest-500 font-display font-bold text-white">{{ $user->initials() }}</div>
                <div class="min-w-0 leading-tight">
                    <div class="truncate text-sm font-bold">{{ $user->name }}</div>
                    <div class="text-[11px] text-forest-200">{{ $settings['manager_name'] }}</div>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <a href="{{ route('home') }}" class="rounded-lg bg-cream/10 py-2 text-center text-xs font-bold transition hover:bg-cream/20">Website ↗</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full rounded-lg bg-cream/10 py-2 text-xs font-bold transition hover:bg-cream/20">Keluar</button>
                </form>
            </div>
        </div>
    </aside>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-40 border-b border-forest-100 bg-cream/90 backdrop-blur">
            <div class="flex items-center gap-3 px-4 py-3 sm:px-6 lg:px-8">
                <img src="{{ asset('images/logo-bumdes-meohai.png') }}" class="h-9 w-9 rounded-full bg-white p-0.5 shadow-sm lg:hidden" alt="Logo">
                <div class="leading-tight">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-harvest-600">Panel Admin</div>
                    <h1 class="font-display text-lg font-extrabold">@yield('title', 'Dashboard')</h1>
                </div>
                <div class="ml-auto flex items-center gap-2">
                    @if ($queueCounts['sellers'] + $queueCounts['products'] > 0)
                        <a href="{{ $queueCounts['sellers'] ? route('admin.verification.index') : route('admin.products.index') }}" class="relative grid h-10 w-10 place-items-center rounded-full border border-forest-100 bg-white" aria-label="Ada {{ $queueCounts['sellers'] + $queueCounts['products'] }} antrean menunggu">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4-5.7V5a2 2 0 10-4 0v.3A6 6 0 006 11v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-clay-500"></span>
                        </a>
                    @endif
                    <div class="grid h-10 w-10 place-items-center rounded-full bg-harvest-500 font-display text-sm font-bold text-white lg:hidden">{{ $user->initials() }}</div>
                </div>
            </div>
        </header>

        <main class="max-w-6xl px-4 py-6 pb-28 sm:px-6 lg:px-8 lg:pb-10">
            @yield('content')
        </main>
    </div>
</div>

{{-- Bottom nav (mobile) --}}
<nav class="pb-safe fixed inset-x-0 bottom-0 z-50 border-t border-forest-100 bg-white/95 backdrop-blur lg:hidden" aria-label="Menu admin">
    <div class="grid grid-cols-5 text-[11px] font-semibold text-forest-500">
        @foreach ([
            ['Beranda', 'admin.dashboard', 'admin.dashboard', 'home', null],
            ['Verifikasi', 'admin.verification.index', 'admin.verification.*', 'check', $queueCounts['sellers']],
            ['Produk', 'admin.products.index', 'admin.products.*', 'box', $queueCounts['products']],
            ['Penjual', 'admin.sellers.index', 'admin.sellers.*', 'users', null],
        ] as [$label, $route, $pattern, $icon, $count])
            <a href="{{ route($route) }}" class="relative flex flex-col items-center gap-1 py-2.5 {{ request()->routeIs($pattern) ? 'text-harvest-600' : '' }}">
                @if ($count)
                    <span class="absolute left-1/2 top-1.5 ml-2 grid h-4 min-w-4 place-items-center rounded-full bg-clay-500 px-1 text-[9px] font-bold text-white">{{ $count }}</span>
                @endif
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons[$icon] }}"/></svg>{{ $label }}
            </a>
        @endforeach
        <button type="button" data-modal-open="admin-more" class="flex flex-col items-center gap-1 py-2.5">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons['cog'] }}"/></svg>Lainnya
        </button>
    </div>
</nav>

<x-modal id="admin-more" title="Menu Admin">
    <div class="mt-4 space-y-4">
        @foreach ($menu as $group => $items)
            <div>
                <div class="mb-2 text-[10px] font-bold uppercase tracking-widest text-forest-400">{{ $group }}</div>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($items as [$label, $route, $pattern, $icon, $count])
                        <a href="{{ route($route) }}" class="flex flex-col items-center gap-1 rounded-xl border border-forest-100 bg-white p-3 text-center text-[11px] font-bold {{ request()->routeIs($pattern) ? 'border-harvest-300 text-harvest-600' : '' }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icons[$icon] }}"/></svg>{{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
        <div class="grid grid-cols-2 gap-2">
            <a href="{{ route('home') }}" class="btn-outline">Website ↗</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-danger w-full">Keluar</button>
            </form>
        </div>
    </div>
</x-modal>

<x-flash />
</body>
</html>
