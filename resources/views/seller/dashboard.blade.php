@extends('layouts.seller')

@section('title', 'Beranda Toko')

@use('App\Enums\ProductStatus')
@use('App\Enums\VerificationStatus')

@php
    $profile = $user->sellerProfile;
    $verification = $profile?->verification_status ?? VerificationStatus::Pending;
    $hour = (int) now()->format('H');
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
@endphp

@section('content')
    <section class="relative overflow-hidden rounded-3xl bg-forest-700 text-cream">
        <div class="absolute -right-16 -top-20 h-60 w-60 rounded-full bg-harvest-500/30 blur-3xl"></div>
        <div class="relative p-6 pb-10">
            <div class="flex items-center gap-3">
                <div class="grid h-12 w-12 place-items-center rounded-full bg-harvest-500 font-display font-extrabold text-white">{{ $user->initials() }}</div>
                <div>
                    <div class="text-xs text-forest-200">{{ $greeting }},</div>
                    <div class="font-display text-lg font-extrabold leading-tight">{{ $user->name }}</div>
                </div>
                <span class="ml-auto rounded-full bg-cream/15 px-3 py-1 text-[10px] font-bold uppercase tracking-widest">
                    {{ $verification === VerificationStatus::Approved ? '✓ ' : '' }}{{ $verification->label() }}
                </span>
            </div>

            @if ($verification === VerificationStatus::Approved)
                <div class="mt-6 text-xs font-bold uppercase tracking-widest text-harvest-300">Pesanan selesai {{ now()->translatedFormat('F') }}</div>
                <div class="mt-1 font-display text-3xl font-extrabold sm:text-4xl">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</div>
                <div class="mt-1 text-sm text-forest-200">dari {{ $monthlyCompleted }} pesanan yang dicatat · {{ $store->name }}</div>
            @else
                <div class="mt-6 font-display text-xl font-extrabold">{{ $store?->name }}</div>
                <div class="mt-1 text-sm text-forest-200">Toko dapat dikelola setelah akun diverifikasi admin L-DCH.</div>
            @endif
        </div>
        <div class="scallop absolute inset-x-0 bottom-0 h-4 bg-cream"></div>
    </section>

    @if ($verification !== VerificationStatus::Approved)
        @php
            $states = [
                'pending' => ['⏳', 'Pendaftaran sedang diperiksa', 'Admin L-DCH sedang memeriksa data Anda. Setelah disetujui, Anda dapat melengkapi profil toko dan mulai menambahkan produk.', 'border-harvest-100 bg-harvest-50'],
                'rejected' => ['📝', 'Pendaftaran perlu diperbaiki', 'Silakan perbaiki data pada halaman Akun, lalu simpan untuk mengajukan ulang.', 'border-clay-400/40 bg-clay-50'],
                'suspended' => ['⛔', 'Akun penjual dinonaktifkan', 'Toko dan produk Anda tidak tampil di katalog sampai diaktifkan kembali oleh admin. Hubungi pengelola L-DCH.', 'border-clay-400/40 bg-clay-50'],
            ];
            [$icon, $title, $text, $classes] = $states[$verification->value];
        @endphp
        <div class="mt-5 flex gap-3 rounded-2xl border p-5 {{ $classes }}">
            <div class="text-2xl" aria-hidden="true">{{ $icon }}</div>
            <div class="flex-1">
                <div class="font-display font-bold">{{ $title }}</div>
                <p class="mt-1 text-sm text-forest-600">{{ $text }}</p>
                @if ($profile?->verification_note)
                    <p class="mt-2 rounded-xl bg-white/70 px-3 py-2 text-sm"><strong>Catatan admin:</strong> {{ $profile->verification_note }}</p>
                @endif
                @if ($verification === VerificationStatus::Rejected)
                    <a href="{{ route('seller.account') }}" class="btn-dark mt-3">Perbaiki Data</a>
                @endif
            </div>
        </div>

        <div class="card mt-5 p-5">
            <h3 class="font-display font-bold">Langkah berikutnya</h3>
            <ol class="mt-3 space-y-2 text-sm text-forest-600">
                <li class="flex gap-2"><span class="font-bold text-forest-700">✓</span> Kirim pendaftaran</li>
                <li class="flex gap-2"><span class="font-bold {{ $verification === VerificationStatus::Pending ? 'text-harvest-600' : 'text-forest-300' }}">2</span> Verifikasi oleh admin L-DCH</li>
                <li class="flex gap-2"><span class="font-bold text-forest-300">3</span> Lengkapi profil toko & nomor WhatsApp</li>
                <li class="flex gap-2"><span class="font-bold text-forest-300">4</span> Tambahkan produk — produk diperiksa admin sebelum tayang</li>
            </ol>
        </div>
    @else
        {{-- Quick actions --}}
        <div class="mt-5 grid grid-cols-4 gap-2 text-center sm:gap-4">
            @foreach ([
                [route('seller.products.create'), '➕', 'Jual Produk', 'bg-harvest-100'],
                [route('seller.orders.create'), '📝', 'Catat Pesanan', 'bg-forest-100'],
                [route('seller.reports.sales'), '📊', 'Laporan', 'bg-forest-100'],
                [route('seller.store.edit'), '🏪', 'Profil Toko', 'bg-clay-50'],
            ] as [$href, $icon, $label, $bg])
                <a href="{{ $href }}" class="group">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl text-2xl transition group-active:scale-95 {{ $bg }}" aria-hidden="true">{{ $icon }}</div>
                    <div class="mt-2 text-[11px] font-bold sm:text-xs">{{ $label }}</div>
                </a>
            @endforeach
        </div>

        {{-- Product status summary --}}
        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @foreach ([ProductStatus::Published, ProductStatus::Pending, ProductStatus::Rejected, ProductStatus::Draft] as $status)
                <a href="{{ route('seller.products.index', ['status' => $status->value]) }}" class="card p-4 transition hover:border-harvest-300">
                    <div class="text-xs font-semibold text-forest-500">{{ $status->label() }}</div>
                    <div class="mt-1 font-display text-2xl font-extrabold">{{ $statusCounts[$status->value] ?? 0 }}</div>
                </a>
            @endforeach
        </div>

        @if (! $store->isOpen())
            <div class="mt-5 flex items-center gap-3 rounded-2xl border border-clay-400/40 bg-clay-50 p-4">
                <div class="text-2xl" aria-hidden="true">🔒</div>
                <div class="flex-1 text-sm"><strong>Toko sedang tutup.</strong> Produk Anda tidak tampil di katalog.</div>
                <form method="POST" action="{{ route('seller.store.toggle') }}">
                    @csrf @method('PATCH')
                    <button class="rounded-full bg-forest-700 px-3 py-2 text-xs font-bold text-cream">Buka Toko</button>
                </form>
            </div>
        @endif

        {{-- Orders needing action --}}
        <div class="mt-6 flex items-center justify-between">
            <h3 class="font-display font-bold">Pesanan Perlu Diproses</h3>
            <a href="{{ route('seller.orders.index') }}" class="text-sm font-bold text-harvest-600">Semua →</a>
        </div>
        <div class="mt-3 space-y-3">
            @forelse ($activeOrders as $order)
                @include('seller.orders.partials.card', ['order' => $order])
            @empty
                <p class="card p-5 text-center text-sm text-forest-500">Tidak ada pesanan aktif. Catat pesanan dari WhatsApp agar rekap Anda rapi. 🎉</p>
            @endforelse
        </div>

        {{-- Low stock --}}
        @foreach ($lowStock as $product)
            <div class="mt-4 flex gap-3 rounded-2xl border border-harvest-100 bg-harvest-50 p-4">
                <div class="text-2xl" aria-hidden="true">⚠️</div>
                <div class="flex-1">
                    <div class="text-sm font-bold">Stok {{ $product->name }} tinggal {{ $product->stock }} {{ $product->unit }}</div>
                    <div class="mt-0.5 text-xs text-forest-500">Perbarui stok agar pembeli tidak kecewa.</div>
                </div>
                <a href="{{ route('seller.products.index', ['q' => $product->name]) }}" class="self-center rounded-full bg-forest-700 px-3 py-2 text-xs font-bold text-cream">Atur</a>
            </div>
        @endforeach

        <div class="card mt-6 p-5">
            <span class="text-[10px] font-bold uppercase tracking-widest text-harvest-600">Tips dari L-DCH</span>
            <h4 class="mt-1 font-display font-bold">Foto produk di bawah cahaya pagi</h4>
            <p class="mt-1 text-sm text-forest-500">Produk dengan foto terang lebih menarik di katalog. Tim Karang Taruna siap membantu memotret produk Anda.</p>
        </div>
    @endif
@endsection
