@extends('layouts.admin')

@section('title', 'Edit Profil Toko')

@use('App\Enums\StoreStatus')

@section('content')
    <a href="{{ route('admin.sellers.show', $seller) }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← {{ $seller->name }}</a>
    <p class="mt-2 max-w-2xl rounded-2xl border border-harvest-100 bg-harvest-50 p-4 text-sm">
        Gunakan hanya bila perlu, misalnya atas permintaan penjual atau untuk memperbaiki data yang keliru. Perubahan tercatat di log aktivitas.
    </p>

    <form method="POST" action="{{ route('admin.stores.update', $seller) }}" class="card mt-4 grid max-w-3xl gap-4 p-5 sm:grid-cols-2">
        @csrf @method('PUT')
        <x-field name="name" label="Nama Toko">
            <input id="name" name="name" value="{{ old('name', $store->name) }}" required maxlength="120" class="form-input">
        </x-field>
        <x-field name="whatsapp_number" label="Nomor WhatsApp Toko">
            <input id="whatsapp_number" name="whatsapp_number" type="tel" value="{{ old('whatsapp_number', \App\Services\WhatsApp::display($store->whatsapp_number)) }}" required class="form-input">
        </x-field>
        <x-field name="address" label="Alamat">
            <input id="address" name="address" value="{{ old('address', $store->address) }}" maxlength="255" class="form-input">
        </x-field>
        <x-field name="business_hours" label="Jam Operasional">
            <input id="business_hours" name="business_hours" value="{{ old('business_hours', $store->business_hours) }}" maxlength="100" class="form-input">
        </x-field>
        <x-field name="description" label="Deskripsi" class="sm:col-span-2">
            <textarea id="description" name="description" rows="4" maxlength="1000" class="form-input">{{ old('description', $store->description) }}</textarea>
        </x-field>
        <x-field name="status" label="Status Toko">
            <select id="status" name="status" class="form-input">
                @foreach (StoreStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $store->status->value) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </x-field>
        <div class="sm:col-span-2"><button class="btn-dark">Simpan Profil Toko</button></div>
    </form>
@endsection
