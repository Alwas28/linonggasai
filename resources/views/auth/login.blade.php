@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <span class="text-[10px] font-bold uppercase tracking-widest text-harvest-600">Penjual & Admin</span>
    <h1 class="mt-1 font-display text-2xl font-extrabold">Masuk ke L-DCH</h1>
    <p class="mt-1 text-sm text-forest-500">Kelola toko, produk, dan pesanan Anda.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <x-field name="email" label="Email">
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-input">
        </x-field>

        <x-field name="password" label="Kata Sandi">
            <input id="password" name="password" type="password" required autocomplete="current-password" class="form-input">
        </x-field>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 font-semibold text-forest-600">
                <input type="checkbox" name="remember" class="rounded border-forest-300 text-harvest-500 focus:ring-harvest-300"> Ingat saya
            </label>
            <a href="{{ route('password.request') }}" class="font-bold text-harvest-600 hover:text-harvest-700">Lupa kata sandi?</a>
        </div>

        <button class="btn-primary w-full py-3.5">Masuk</button>
    </form>

    <p class="mt-6 text-center text-sm text-forest-500">
        Warga Linonggasai dan ingin berjualan?
        <a href="{{ route('seller.register') }}" class="font-bold text-harvest-600 hover:text-harvest-700">Daftar sebagai penjual</a>
    </p>
@endsection
