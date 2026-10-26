@extends('layouts.seller')

@section('title', 'Akun')

@use('App\Enums\VerificationStatus')

@section('content')
    @php($profile = $user->sellerProfile)

    <div class="card p-6 text-center">
        <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-harvest-500 font-display text-2xl font-extrabold text-white">{{ $user->initials() }}</div>
        <h1 class="mt-3 font-display text-xl font-extrabold">{{ $user->name }}</h1>
        <p class="text-sm text-forest-500">{{ $user->store?->name }} · {{ $profile?->village }}, Linonggasai</p>
        @if ($profile)
            <x-badge :status="$profile->verification_status" class="mt-3" />
        @endif
    </div>

    @if ($profile?->verification_status === VerificationStatus::Rejected)
        <div class="mt-4 rounded-2xl border border-clay-400/40 bg-clay-50 p-4 text-sm">
            <strong>Pendaftaran ditolak.</strong> {{ $profile->verification_note }}
            <div class="mt-1 text-forest-600">Perbaiki data di bawah lalu simpan untuk mengajukan ulang verifikasi.</div>
        </div>
    @endif

    <form method="POST" action="{{ route('seller.account.update') }}" class="card mt-4 grid gap-4 p-5 sm:grid-cols-2">
        @csrf @method('PUT')
        <h2 class="font-display font-bold sm:col-span-2">Data Diri & Domisili</h2>
        <x-field name="name" label="Nama Lengkap">
            <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="form-input">
        </x-field>
        <x-field name="email" label="Email">
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="form-input">
        </x-field>
        <x-field name="phone" label="Nomor HP">
            <input id="phone" name="phone" type="tel" value="{{ old('phone', \App\Services\WhatsApp::display($user->phone)) }}" required class="form-input">
        </x-field>
        <x-field name="village" label="Dusun">
            <input id="village" name="village" value="{{ old('village', $profile?->village) }}" required class="form-input">
        </x-field>
        <x-field name="address" label="Alamat" class="sm:col-span-2">
            <input id="address" name="address" value="{{ old('address', $profile?->address) }}" required class="form-input">
        </x-field>
        <div class="sm:col-span-2">
            <button class="btn-dark">{{ $profile?->verification_status === VerificationStatus::Rejected ? 'Simpan & Ajukan Ulang' : 'Simpan Data' }}</button>
        </div>
    </form>

    <div class="card mt-4 divide-y divide-forest-100">
        @if ($user->isApprovedSeller())
            <a href="{{ route('seller.store.edit') }}" class="flex items-center gap-3 px-5 py-4"><span class="text-xl" aria-hidden="true">🏪</span><span class="flex-1 text-sm font-semibold">Profil Toko & Nomor WhatsApp</span><span class="text-forest-300">›</span></a>
            <a href="{{ route('stores.show', $user->store) }}" class="flex items-center gap-3 px-5 py-4"><span class="text-xl" aria-hidden="true">🌐</span><span class="flex-1 text-sm font-semibold">Lihat Halaman Toko Publik</span><span class="text-forest-300">›</span></a>
        @endif
        <a href="{{ route('seller.settings') }}" class="flex items-center gap-3 px-5 py-4"><span class="text-xl" aria-hidden="true">🔑</span><span class="flex-1 text-sm font-semibold">Pengaturan & Kata Sandi</span><span class="text-forest-300">›</span></a>
        <a href="{{ route('pages.contact') }}" class="flex items-center gap-3 px-5 py-4"><span class="text-xl" aria-hidden="true">🤝</span><span class="flex-1 text-sm font-semibold">Hubungi Admin L-DCH</span><span class="text-forest-300">›</span></a>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button class="btn-danger w-full py-3">Keluar</button>
    </form>
@endsection
