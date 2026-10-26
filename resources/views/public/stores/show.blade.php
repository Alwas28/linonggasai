@extends('layouts.public')

@section('title', $store->name)

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12">
        <div class="relative overflow-hidden rounded-3xl bg-forest-700 text-cream">
            <div class="absolute -right-16 -top-20 h-60 w-60 rounded-full bg-harvest-500/30 blur-3xl"></div>
            <div class="relative flex flex-col gap-5 p-6 pb-10 sm:flex-row sm:items-center sm:p-8 sm:pb-12">
                @if ($store->logoUrl())
                    <img src="{{ $store->logoUrl() }}" alt="{{ $store->name }}" class="h-20 w-20 rounded-full bg-white object-cover p-0.5">
                @else
                    <div class="grid h-20 w-20 place-items-center rounded-full bg-harvest-500 font-display text-2xl font-extrabold text-white">{{ $store->initials() }}</div>
                @endif
                <div class="flex-1">
                    <span class="rounded-full bg-cream/15 px-3 py-1 text-[10px] font-bold uppercase tracking-widest">✓ Penjual Terverifikasi</span>
                    <h1 class="mt-2 font-display text-3xl font-extrabold">{{ $store->name }}</h1>
                    <p class="mt-1 text-sm text-forest-200">Pemilik: {{ $store->user->name }}{{ $store->user->sellerProfile?->village ? ' · '.$store->user->sellerProfile->village : '' }}</p>
                </div>
                <a href="{{ $store->whatsappUrl('Halo '.$store->name.', saya melihat toko Anda di L-DCH Linonggasai.') }}" target="_blank" rel="noopener" class="btn-primary">💬 Hubungi Toko</a>
            </div>
            <div class="scallop absolute inset-x-0 bottom-0 h-4 bg-cream"></div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="card p-5 md:col-span-2">
                <h2 class="font-display font-bold">Tentang Toko</h2>
                <p class="mt-2 whitespace-pre-line text-sm text-forest-600">{{ $store->description ?: 'Toko ini belum menambahkan deskripsi.' }}</p>
            </div>
            <div class="card space-y-2 p-5 text-sm">
                <div><span class="text-forest-400">📍 Alamat</span><div class="font-semibold">{{ $store->address ?: '-' }}</div></div>
                <div><span class="text-forest-400">🕒 Jam operasional</span><div class="font-semibold">{{ $store->business_hours ?: '-' }}</div></div>
            </div>
        </div>

        <h2 class="mt-10 font-display text-2xl font-extrabold">Produk dari {{ $store->name }}</h2>
        @if ($products->isEmpty())
            <x-empty-state class="mt-6" icon="🧺" title="Belum ada produk tayang" />
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            {{ $products->links() }}
        @endif
    </section>
@endsection
