@extends('layouts.admin')

@use('App\Enums\CategoryStatus')

@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
    <a href="{{ route('admin.categories.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Kategori</a>

    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="card mt-3 grid max-w-2xl gap-4 p-5 sm:grid-cols-2">
        @csrf
        @if ($category->exists)
            @method('PUT')
        @endif
        <x-field name="name" label="Nama Kategori">
            <input id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="100" class="form-input">
        </x-field>
        <x-field name="slug" label="Slug (URL)" hint="Kosongkan untuk dibuat otomatis dari nama.">
            <input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" maxlength="120" class="form-input">
        </x-field>
        <x-field name="icon" label="Ikon (emoji)">
            <input id="icon" name="icon" value="{{ old('icon', $category->icon) }}" maxlength="16" class="form-input" placeholder="🌾">
        </x-field>
        <x-field name="sort_order" label="Urutan Tampil">
            <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="form-input">
        </x-field>
        <x-field name="description" label="Deskripsi" class="sm:col-span-2">
            <textarea id="description" name="description" rows="3" maxlength="500" class="form-input">{{ old('description', $category->description) }}</textarea>
        </x-field>
        <x-field name="status" label="Status">
            <select id="status" name="status" class="form-input">
                @foreach (CategoryStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $category->status?->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </x-field>
        <div class="sm:col-span-2">
            <button class="btn-dark">Simpan Kategori</button>
        </div>
    </form>
@endsection
