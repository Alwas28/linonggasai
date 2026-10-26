@extends('layouts.admin')

@section('title', 'Edit Produk')

@use('App\Enums\StockStatus')

@section('content')
    <a href="{{ route('admin.products.show', $product) }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← {{ $product->name }}</a>
    <p class="mt-2 max-w-2xl rounded-2xl border border-harvest-100 bg-harvest-50 p-4 text-sm">
        Koreksi oleh admin untuk produk milik <strong>{{ $product->store->name }}</strong>. Status publikasi tidak berubah dan perubahan tercatat di log aktivitas.
    </p>

    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.products.update', $product) }}" class="card mt-4 grid max-w-3xl gap-4 p-5 sm:grid-cols-2">
        @csrf @method('PUT')

        <x-field name="name" label="Nama Produk" class="sm:col-span-2">
            <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="150" class="form-input">
        </x-field>
        <x-field name="category_id" label="Kategori">
            <select id="category_id" name="category_id" class="form-input">
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((int) old('category_id', $product->category_id) === $category->id)>{{ $category->icon }} {{ $category->name }}</option>
                @endforeach
            </select>
        </x-field>
        <div class="grid grid-cols-2 gap-3">
            <x-field name="price" label="Harga (Rp)">
                <input id="price" name="price" type="number" min="0" value="{{ old('price', (int) $product->price) }}" required class="form-input">
            </x-field>
            <x-field name="unit" label="Satuan">
                <input id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required maxlength="30" class="form-input">
            </x-field>
        </div>
        <x-field name="stock" label="Stok">
            <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock) }}" class="form-input">
        </x-field>
        <x-field name="stock_status" label="Ketersediaan">
            <select id="stock_status" name="stock_status" class="form-input">
                @foreach (StockStatus::cases() as $stockStatus)
                    <option value="{{ $stockStatus->value }}" @selected(old('stock_status', $product->stock_status->value) === $stockStatus->value)>{{ $stockStatus->label() }}</option>
                @endforeach
            </select>
        </x-field>
        <x-field name="description" label="Deskripsi" class="sm:col-span-2">
            <textarea id="description" name="description" rows="5" required maxlength="5000" class="form-input">{{ old('description', $product->description) }}</textarea>
        </x-field>
        <x-field name="main_image" label="Ganti Foto Utama (opsional)" class="sm:col-span-2">
            <input id="main_image" type="file" name="main_image" accept="image/jpeg,image/png,image/webp" class="mt-1 block text-xs file:mr-3 file:rounded-full file:border-0 file:bg-forest-700 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-cream">
        </x-field>
        <div class="sm:col-span-2"><button class="btn-dark">Simpan Perubahan</button></div>
    </form>
@endsection
