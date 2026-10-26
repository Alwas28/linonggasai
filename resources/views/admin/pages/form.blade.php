@extends('layouts.admin')

@section('title', 'Edit Halaman')

@section('content')
    <a href="{{ route('admin.pages.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Halaman Informasi</a>

    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="card mt-3 grid max-w-3xl gap-4 p-5">
        @csrf @method('PUT')
        <div class="text-xs font-bold uppercase tracking-widest text-harvest-600">/{{ $page->slug }}</div>
        <x-field name="title" label="Judul">
            <input id="title" name="title" value="{{ old('title', $page->title) }}" required maxlength="150" class="form-input">
        </x-field>
        <x-field name="content" label="Isi Halaman" hint="Teks biasa. Pisahkan paragraf dengan satu baris kosong.">
            <textarea id="content" name="content" rows="14" required maxlength="10000" class="form-input">{{ old('content', $page->content) }}</textarea>
        </x-field>
        <div class="flex items-center gap-3">
            <button class="btn-dark">Simpan Halaman</button>
            <a href="{{ route(['tentang' => 'pages.about', 'cara-pesan' => 'pages.how-to-order', 'lokasi' => 'pages.location', 'kontak' => 'pages.contact'][$page->slug]) }}" target="_blank" class="text-sm font-bold text-harvest-600">Lihat ↗</a>
        </div>
    </form>
@endsection
