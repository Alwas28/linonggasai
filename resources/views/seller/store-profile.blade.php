@extends('layouts.seller')

@section('title', 'Profil Toko')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-2xl font-extrabold">Profil Toko</h1>
            <p class="text-sm text-forest-500">Informasi ini tampil di halaman toko dan detail produk.</p>
        </div>
        <a href="{{ route('stores.show', $store) }}" target="_blank" class="btn-outline">Lihat Halaman Toko ↗</a>
    </div>

    <div class="card mt-5 flex items-center gap-3 px-5 py-4">
        <span class="text-xl" aria-hidden="true">{{ $store->isOpen() ? '🟢' : '🔒' }}</span>
        <div class="flex-1">
            <div class="text-sm font-semibold">Toko {{ $store->isOpen() ? 'Buka' : 'Tutup' }}</div>
            <div class="text-xs text-forest-500">{{ $store->isOpen() ? 'Produk yang disetujui tampil di katalog.' : 'Produk disembunyikan dari katalog.' }}</div>
        </div>
        <form method="POST" action="{{ route('seller.store.toggle') }}">
            @csrf @method('PATCH')
            <button class="relative h-7 w-12 rounded-full transition {{ $store->isOpen() ? 'bg-forest-500' : 'bg-forest-200' }}" role="switch" aria-checked="{{ $store->isOpen() ? 'true' : 'false' }}" aria-label="Buka atau tutup toko">
                <span class="absolute top-1 h-5 w-5 rounded-full bg-white transition-all {{ $store->isOpen() ? 'left-6' : 'left-1' }}"></span>
            </button>
        </form>
    </div>

    <form method="POST" action="{{ route('seller.store.update') }}" enctype="multipart/form-data" class="card mt-4 grid gap-4 p-5 sm:grid-cols-2">
        @csrf @method('PUT')

        <div class="flex items-center gap-4 sm:col-span-2">
            @if ($store->logoUrl())
                <img id="logo-preview" src="{{ $store->logoUrl() }}" alt="Logo toko" class="h-16 w-16 rounded-full object-cover">
            @else
                <img id="logo-preview" src="" alt="" class="hidden h-16 w-16 rounded-full object-cover">
                <div id="logo-placeholder" class="grid h-16 w-16 place-items-center rounded-full bg-harvest-500 font-display text-xl font-extrabold text-white">{{ $store->initials() }}</div>
            @endif
            <div>
                <label for="logo" class="form-label">Foto / Logo Toko</label>
                <input id="logo" type="file" name="logo" accept="image/jpeg,image/png,image/webp" data-preview="#logo-preview" data-preview-hide="#logo-placeholder" class="mt-1 block text-xs file:mr-3 file:rounded-full file:border-0 file:bg-forest-700 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-cream">
                @error('logo')
                    <p class="mt-1 text-xs font-semibold text-clay-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <x-field name="name" label="Nama Toko">
            <input id="name" name="name" value="{{ old('name', $store->name) }}" required maxlength="120" class="form-input">
        </x-field>
        <x-field name="whatsapp_number" label="Nomor WhatsApp Toko" hint="Tombol pesan di katalog akan membuka nomor ini.">
            <input id="whatsapp_number" name="whatsapp_number" type="tel" value="{{ old('whatsapp_number', \App\Services\WhatsApp::display($store->whatsapp_number)) }}" required class="form-input">
        </x-field>
        <x-field name="address" label="Alamat">
            <input id="address" name="address" value="{{ old('address', $store->address) }}" maxlength="255" class="form-input">
        </x-field>
        <x-field name="business_hours" label="Jam Operasional">
            <input id="business_hours" name="business_hours" value="{{ old('business_hours', $store->business_hours) }}" maxlength="100" class="form-input" placeholder="cth. Setiap hari, 07.00 – 17.00 WITA">
        </x-field>
        <x-field name="description" label="Deskripsi Toko" class="sm:col-span-2">
            <textarea id="description" name="description" rows="4" maxlength="1000" class="form-input">{{ old('description', $store->description) }}</textarea>
        </x-field>
        <x-field name="current_password" label="Kata Sandi Saat Ini" hint="Wajib diisi hanya jika Anda mengganti nomor WhatsApp toko." class="sm:col-span-2">
            <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="form-input">
        </x-field>

        <div class="sm:col-span-2">
            <button class="btn-dark w-full sm:w-auto">Simpan Profil Toko</button>
        </div>
    </form>
@endsection
