@extends('layouts.admin')

@section('title', $banner->exists ? 'Edit Banner' : 'Tambah Banner')

@section('content')
    <a href="{{ route('admin.banners.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Banner</a>

    <form method="POST" enctype="multipart/form-data" action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" class="card mt-3 grid max-w-2xl gap-4 p-5">
        @csrf
        @if ($banner->exists)
            @method('PUT')
        @endif

        <div>
            <span class="form-label">Gambar (rasio 16:9 disarankan, maks. 2 MB)</span>
            <label class="mt-1 block cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed border-forest-200 bg-white text-center hover:border-harvest-300">
                <img id="banner-preview" src="{{ $banner->exists ? $banner->imageUrl() : '' }}" alt="" class="{{ $banner->exists ? '' : 'hidden' }} aspect-[16/9] w-full object-cover">
                <div id="banner-placeholder" class="{{ $banner->exists ? 'hidden' : '' }} py-10 text-sm font-bold">🖼️ Pilih gambar</div>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only" data-preview="#banner-preview" data-preview-hide="#banner-placeholder">
            </label>
            @error('image')
                <p class="mt-1 text-xs font-semibold text-clay-600">{{ $message }}</p>
            @enderror
        </div>

        <x-field name="title" label="Judul">
            <input id="title" name="title" value="{{ old('title', $banner->title) }}" required maxlength="150" class="form-input">
        </x-field>
        <x-field name="subtitle" label="Subjudul">
            <input id="subtitle" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" maxlength="255" class="form-input">
        </x-field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-field name="link_url" label="Tautan (opsional)" hint="Contoh: /kategori/perikanan atau https://…">
                <input id="link_url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" maxlength="255" class="form-input">
            </x-field>
            <x-field name="sort_order" label="Urutan">
                <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="form-input">
            </x-field>
        </div>
        <label class="flex items-center gap-2 text-sm font-semibold">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active)) class="rounded border-forest-300 text-harvest-500 focus:ring-harvest-300"> Tampilkan di beranda
        </label>
        <div><button class="btn-dark">Simpan Banner</button></div>
    </form>
@endsection
