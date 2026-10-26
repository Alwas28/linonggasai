@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
    <form method="POST" action="{{ route('admin.settings.update') }}" class="grid gap-4 lg:grid-cols-2">
        @csrf @method('PUT')

        <div class="card space-y-4 p-5">
            <h2 class="font-display font-bold">Konten Beranda</h2>
            <x-field name="hero_badge" label="Label Hero">
                <input id="hero_badge" name="hero_badge" value="{{ old('hero_badge', $values['hero_badge']) }}" maxlength="100" class="form-input">
            </x-field>
            <x-field name="hero_title" label="Judul Hero">
                <input id="hero_title" name="hero_title" value="{{ old('hero_title', $values['hero_title']) }}" required maxlength="150" class="form-input">
            </x-field>
            <x-field name="hero_description" label="Deskripsi Hero">
                <textarea id="hero_description" name="hero_description" rows="3" required maxlength="500" class="form-input">{{ old('hero_description', $values['hero_description']) }}</textarea>
            </x-field>
        </div>

        <div class="card space-y-4 p-5">
            <h2 class="font-display font-bold">Identitas & Kontak</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-field name="site_name" label="Nama Website">
                    <input id="site_name" name="site_name" value="{{ old('site_name', $values['site_name']) }}" required maxlength="100" class="form-input">
                </x-field>
                <x-field name="site_tagline" label="Tagline">
                    <input id="site_tagline" name="site_tagline" value="{{ old('site_tagline', $values['site_tagline']) }}" maxlength="100" class="form-input">
                </x-field>
                <x-field name="manager_name" label="Pengelola">
                    <input id="manager_name" name="manager_name" value="{{ old('manager_name', $values['manager_name']) }}" required maxlength="100" class="form-input">
                </x-field>
                <x-field name="admin_whatsapp" label="WhatsApp Pengelola">
                    <input id="admin_whatsapp" name="admin_whatsapp" type="tel" value="{{ old('admin_whatsapp', $values['admin_whatsapp'] ? \App\Services\WhatsApp::display($values['admin_whatsapp']) : '') }}" class="form-input" placeholder="08xxxxxxxxxx">
                </x-field>
                <x-field name="contact_email" label="Email Kontak" class="sm:col-span-2">
                    <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $values['contact_email']) }}" class="form-input">
                </x-field>
                <x-field name="address" label="Alamat" class="sm:col-span-2">
                    <input id="address" name="address" value="{{ old('address', $values['address']) }}" required maxlength="255" class="form-input">
                </x-field>
            </div>
        </div>

        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center justify-between gap-4 rounded-xl bg-forest-50 px-4 py-3">
                <div>
                    <div class="text-sm font-bold">Terima pendaftaran penjual baru</div>
                    <div class="text-xs text-forest-500">Matikan untuk menutup sementara formulir Daftar Penjual.</div>
                </div>
                <input type="hidden" name="accept_seller_registration" value="0">
                <input type="checkbox" name="accept_seller_registration" value="1" @checked(old('accept_seller_registration', $values['accept_seller_registration']) === '1') class="h-6 w-6 rounded border-forest-300 text-forest-500 focus:ring-harvest-300" aria-label="Terima pendaftaran penjual baru">
            </div>
            <button class="btn-dark mt-4">Simpan Pengaturan</button>
        </div>
    </form>

    <div class="mt-4 max-w-xl">
        @include('partials.password-form')
    </div>
@endsection
