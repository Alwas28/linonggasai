@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi')

@section('content')
    <h1 class="font-display text-2xl font-extrabold">Atur ulang kata sandi</h1>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-field name="email" label="Email">
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="username" class="form-input">
        </x-field>
        <x-field name="password" label="Kata Sandi Baru">
            <input id="password" name="password" type="password" required autocomplete="new-password" class="form-input">
        </x-field>
        <x-field name="password_confirmation" label="Ulangi Kata Sandi Baru">
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="form-input">
        </x-field>
        <button class="btn-primary w-full py-3.5">Simpan Kata Sandi</button>
    </form>
@endsection
