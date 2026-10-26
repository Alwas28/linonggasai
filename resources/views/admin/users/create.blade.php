@extends('layouts.admin')

@section('title', 'Tambah Akun Admin')

@section('content')
    <a href="{{ route('admin.users.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Pengguna</a>
    <p class="mt-2 max-w-2xl text-sm text-forest-500">Akun admin hanya dapat dibuat oleh admin lain. Tidak ada pendaftaran admin publik.</p>

    <form method="POST" action="{{ route('admin.users.store') }}" class="card mt-3 grid max-w-2xl gap-4 p-5 sm:grid-cols-2">
        @csrf
        <x-field name="name" label="Nama">
            <input id="name" name="name" value="{{ old('name') }}" required class="form-input">
        </x-field>
        <x-field name="email" label="Email">
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="form-input">
        </x-field>
        <x-field name="phone" label="Nomor HP (opsional)">
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" class="form-input">
        </x-field>
        <div class="hidden sm:block"></div>
        <x-field name="password" label="Kata Sandi">
            <input id="password" name="password" type="password" required autocomplete="new-password" class="form-input">
        </x-field>
        <x-field name="password_confirmation" label="Ulangi Kata Sandi">
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="form-input">
        </x-field>
        <div class="sm:col-span-2"><button class="btn-dark">Buat Akun Admin</button></div>
    </form>
@endsection
