@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <h1 class="font-display text-2xl font-extrabold">Lupa kata sandi?</h1>
    <p class="mt-1 text-sm text-forest-500">Masukkan email akun Anda. Kami akan mengirim tautan untuk mengatur ulang kata sandi.</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <x-field name="email" label="Email">
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="form-input">
        </x-field>
        <button class="btn-primary w-full py-3.5">Kirim Tautan Reset</button>
    </form>

    <p class="mt-6 text-center text-sm"><a href="{{ route('login') }}" class="font-bold text-harvest-600 hover:text-harvest-700">← Kembali ke halaman masuk</a></p>
@endsection
