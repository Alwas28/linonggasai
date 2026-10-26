@extends('layouts.admin')

@section('title', 'Dashboard')

@php
    $hour = (int) now()->format('H');
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $barColors = ['bg-forest-500', 'bg-harvest-500', 'bg-clay-500', 'bg-sky-600', 'bg-forest-300'];
@endphp

@section('content')
    <section class="relative overflow-hidden rounded-3xl bg-forest-700 p-6 text-cream">
        <div class="absolute -right-16 -top-16 h-56 w-56 rounded-full bg-harvest-500/30 blur-3xl"></div>
        <div class="relative">
            <div class="text-xs font-bold uppercase tracking-widest text-harvest-300">{{ now()->translatedFormat('l, d F Y') }}</div>
            <h2 class="mt-1 font-display text-2xl font-extrabold">{{ $greeting }}, {{ auth()->user()->name }} 👋</h2>
            <p class="mt-1 max-w-md text-sm text-forest-100">
                Ada {{ $sellerCounts['pending'] ?? 0 }} penjual menunggu verifikasi dan {{ $productCounts['pending'] ?? 0 }} produk menunggu moderasi.
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('admin.verification.index') }}" class="rounded-full bg-harvest-500 px-4 py-2 text-sm font-bold text-white transition hover:bg-harvest-600">Verifikasi Penjual</a>
                <a href="{{ route('admin.products.index') }}" class="rounded-full border border-cream/40 px-4 py-2 text-sm font-bold transition hover:bg-cream/10">Moderasi Produk</a>
            </div>
        </div>
    </section>

    @php
        $change = $lastMonthRevenue > 0 ? ($monthSales['revenue'] - $lastMonthRevenue) / $lastMonthRevenue * 100 : null;
        $salesNote = $change === null
            ? $monthSales['orders'].' pesanan selesai'
            : ($change >= 0 ? '▲ ' : '▼ ').number_format(abs($change), 0, ',', '.').'% dari bulan lalu';
    @endphp
    <div class="mt-5 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <x-stat-card label="Penjualan Bulan Ini" :value="'Rp '.number_format($monthSales['revenue'], 0, ',', '.')" :note="$salesNote" :note-class="$change !== null && $change < 0 ? 'text-clay-500' : 'text-forest-400'" :href="route('admin.reports.sales', ['periode' => 'bulan-ini'])" />
        <x-stat-card label="Penjual Aktif" :value="$sellerCounts['approved'] ?? 0" :note="($sellerCounts['pending'] ?? 0).' menunggu verifikasi'" note-class="text-clay-500" :href="route('admin.sellers.index')" />
        <x-stat-card label="Produk Tayang" :value="$productCounts['published'] ?? 0" :note="$categories->where('status.value', 'active')->count().' kategori aktif'" :href="route('admin.products.index', ['status' => 'published'])" />
        <x-stat-card label="Menunggu Moderasi" :value="$productCounts['pending'] ?? 0" :note="$orderCount.' pesanan tercatat'" note-class="text-harvest-600" :href="route('admin.products.index')" />
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-5">
        <div class="lg:col-span-3">
            @include('partials.sales.chart', ['timeline' => $weekTimeline, 'title' => 'Penjualan 7 Hari Terakhir'])
        </div>
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between">
                <h3 class="font-display font-bold">Penjualan per Kategori</h3>
                <a href="{{ route('admin.reports.sales', ['periode' => 'bulan-ini']) }}" class="text-xs font-bold text-harvest-600">Bulan ini →</a>
            </div>
            @php($categorySalesTotal = max(1.0, (float) $monthCategories->sum('revenue')))
            @if ($monthCategories->isEmpty())
                <p class="mt-4 text-sm text-forest-500">Belum ada penjualan bulan ini.</p>
            @else
                <div class="mt-4 space-y-4 text-sm">
                    @foreach ($monthCategories as $category)
                        @php($percent = (int) round((float) $category->revenue / $categorySalesTotal * 100))
                        <div>
                            <div class="flex justify-between font-semibold"><span>{{ $category->icon }} {{ $category->name }}</span><span>{{ $percent }}%</span></div>
                            <div class="mt-1.5 h-2.5 rounded-full bg-forest-50" title="Rp {{ number_format((float) $category->revenue, 0, ',', '.') }}"><div class="h-full rounded-full bg-forest-500" style="width: {{ max(2, $percent) }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-5">
        <div class="card p-5 lg:col-span-3">
            <div class="flex items-center justify-between">
                <h3 class="font-display font-bold">Antrean Moderasi Produk</h3>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-harvest-600">Semua →</a>
            </div>
            <div class="mt-3 divide-y divide-forest-100">
                @forelse ($pendingProducts as $product)
                    <a href="{{ route('admin.products.show', $product) }}" class="flex items-center gap-3 py-3">
                        <x-product-thumb :product="$product" class="h-11 w-11 shrink-0 rounded-xl" icon-size="text-xl" />
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-bold">{{ $product->name }}</div>
                            <div class="truncate text-xs text-forest-500">{{ $product->store->name }} · {{ $product->updated_at->diffForHumans() }}</div>
                        </div>
                        <span class="text-sm font-bold text-harvest-600">Periksa →</span>
                    </a>
                @empty
                    <p class="py-6 text-center text-sm text-forest-500">Tidak ada produk menunggu moderasi ✓</p>
                @endforelse
            </div>
        </div>
        <div class="card p-5 lg:col-span-2">
            <h3 class="font-display font-bold">Produk Tayang per Kategori</h3>
            <div class="mt-4 space-y-4 text-sm">
                @foreach ($categories as $category)
                    @php($percent = (int) round($category->products_count / $publishedTotal * 100))
                    <div>
                        <div class="flex justify-between font-semibold"><span>{{ $category->icon }} {{ $category->name }}</span><span>{{ $category->products_count }}</span></div>
                        <div class="mt-1.5 h-2.5 rounded-full bg-forest-50"><div class="h-full rounded-full {{ $barColors[$loop->index % count($barColors)] }}" style="width: {{ $percent }}%"></div></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-2">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <h3 class="font-display font-bold">Pendaftar Penjual</h3>
                <a href="{{ route('admin.verification.index') }}" class="text-sm font-bold text-harvest-600">Periksa →</a>
            </div>
            <div class="mt-3 space-y-2">
                @forelse ($pendingSellers as $profile)
                    <div class="flex items-center gap-3 rounded-xl bg-harvest-50 p-3">
                        <div class="grid h-10 w-10 place-items-center rounded-full bg-harvest-100 font-display font-bold text-harvest-700">{{ $profile->user->initials() }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-bold">{{ $profile->user->name }}</div>
                            <div class="truncate text-xs text-forest-500">{{ $profile->user->store?->name }} · {{ $profile->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-forest-500">Semua pendaftar sudah diverifikasi ✓</p>
                @endforelse
            </div>
        </div>
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <h3 class="font-display font-bold">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.activity') }}" class="text-sm font-bold text-harvest-600">Log →</a>
            </div>
            <div class="mt-3 divide-y divide-forest-100">
                @forelse ($activities as $log)
                    @include('admin.partials.activity-row', ['log' => $log])
                @empty
                    <p class="py-6 text-center text-sm text-forest-500">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
