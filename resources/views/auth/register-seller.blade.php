@extends('layouts.auth', ['width' => '2xl'])

@section('title', 'Daftar Penjual')

@section('content')
    <span class="text-[10px] font-bold uppercase tracking-widest text-harvest-600">Pendaftaran Penjual</span>
    <h1 class="mt-1 font-display text-2xl font-extrabold">Jualan di L-DCH Linonggasai</h1>
    <p class="mt-1 text-sm text-forest-500">Isi data di bawah. Admin L-DCH akan memeriksa data Anda sebelum toko dapat menerbitkan produk.</p>

    @if (! $registrationOpen)
        <div class="mt-6 rounded-2xl border border-harvest-100 bg-harvest-50 p-5 text-sm text-forest-600">
            <strong>Pendaftaran penjual sedang ditutup.</strong> Silakan hubungi pengelola L-DCH untuk informasi lebih lanjut.
        </div>
    @else
        <ol class="mt-5 grid grid-cols-3 gap-2 text-center text-[11px] font-bold text-forest-500">
            <li class="rounded-xl bg-forest-700 px-2 py-2 text-cream">1. Isi data</li>
            <li class="rounded-xl bg-forest-50 px-2 py-2">2. Verifikasi admin</li>
            <li class="rounded-xl bg-forest-50 px-2 py-2">3. Mulai berjualan</li>
        </ol>

        <form method="POST" action="{{ route('seller.register') }}" class="mt-6 space-y-5">
            @csrf
            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-2 font-display text-sm font-bold">Data Akun</legend>
                <x-field name="name" label="Nama Lengkap" class="sm:col-span-2">
                    <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="form-input">
                </x-field>
                <x-field name="email" label="Email">
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" class="form-input">
                </x-field>
                <x-field name="phone" label="Nomor HP / WhatsApp" hint="Digunakan sebagai nomor WhatsApp toko. Contoh: 081234567890">
                    <input id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" required autocomplete="tel" class="form-input" placeholder="08xxxxxxxxxx">
                </x-field>
                <x-field name="password" label="Kata Sandi" hint="Minimal 8 karakter.">
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="form-input">
                </x-field>
                <x-field name="password_confirmation" label="Ulangi Kata Sandi">
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="form-input">
                </x-field>
            </fieldset>

            <fieldset class="grid gap-4 sm:grid-cols-2">
                <legend class="mb-2 font-display text-sm font-bold">Data Usaha & Domisili</legend>
                <x-field name="store_name" label="Nama Usaha / Toko" class="sm:col-span-2">
                    <input id="store_name" name="store_name" value="{{ old('store_name') }}" required class="form-input" placeholder="cth. Beras & Pupuk Pak Hasan">
                </x-field>
                <x-field name="address" label="Alamat">
                    <input id="address" name="address" value="{{ old('address') }}" required autocomplete="street-address" class="form-input">
                </x-field>
                <x-field name="village" label="Dusun">
                    <input id="village" name="village" value="{{ old('village') }}" required class="form-input" placeholder="cth. Dusun II">
                </x-field>
            </fieldset>

            <div>
                <label class="flex items-start gap-3 text-sm text-forest-600">
                    <input type="checkbox" name="terms" value="1" @checked(old('terms')) class="mt-0.5 rounded border-forest-300 text-harvest-500 focus:ring-harvest-300">
                    <span>Saya menyatakan data yang diisi benar, merupakan warga Desa Linonggasai, dan bersedia mematuhi ketentuan L-DCH: produk diperiksa admin sebelum tampil, serta transaksi dan pembayaran dilakukan langsung antara saya dan pembeli.</span>
                </label>
                @error('terms')
                    <p class="mt-1 text-xs font-semibold text-clay-600">{{ $message }}</p>
                @enderror
            </div>

            <button class="btn-primary w-full py-3.5">Kirim Pendaftaran</button>
        </form>
    @endif

    <p class="mt-6 text-center text-sm text-forest-500">
        Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-harvest-600 hover:text-harvest-700">Masuk</a>
    </p>
@endsection
