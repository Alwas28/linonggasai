@extends('layouts.seller')

@use('App\Enums\ProductStatus')
@use('App\Enums\StockStatus')
@use('App\Http\Requests\Seller\ProductRequest')

@php($editing = $product->exists)

@section('title', $editing ? 'Edit Produk' : 'Jual Produk Baru')

@section('content')
    <a href="{{ route('seller.products.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Produk Saya</a>
    <h1 class="mt-2 font-display text-2xl font-extrabold">{{ $editing ? 'Edit '.$product->name : 'Jual Produk Baru' }}</h1>
    <p class="text-sm text-forest-500">Produk akan ditinjau admin L-DCH sebelum tampil di katalog.</p>

    @if ($editing && $product->status === ProductStatus::Published)
        <div class="mt-4 rounded-2xl border border-harvest-100 bg-harvest-50 p-4 text-sm">
            Produk ini sedang <strong>tayang</strong>. Jika perubahan diajukan, produk disembunyikan sementara sampai admin menyetujui perubahan. Untuk mengubah stok saja, gunakan kolom stok di halaman Produk Saya.
        </div>
    @endif
    @if ($editing && $product->status === ProductStatus::Rejected && $product->moderation_note)
        <div class="mt-4 rounded-2xl border border-clay-400/40 bg-clay-50 p-4 text-sm text-clay-600">
            <strong>Alasan ditolak:</strong> {{ $product->moderation_note }}
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $editing ? route('seller.products.update', $product) : route('seller.products.store') }}"
          class="mt-5 grid gap-5 lg:grid-cols-5">
        @csrf
        @if ($editing)
            @method('PUT')
        @endif

        {{-- Photos --}}
        <div class="space-y-4 lg:col-span-2">
            <div>
                <label class="block cursor-pointer rounded-2xl border-2 border-dashed border-forest-200 bg-white p-4 text-center transition hover:border-harvest-300">
                    <img id="main-preview" src="{{ $product->mainImageUrl() }}" alt="" class="{{ $product->mainImageUrl() ? '' : 'hidden' }} mx-auto aspect-square w-full rounded-xl object-cover">
                    <div id="main-placeholder" class="{{ $product->mainImageUrl() ? 'hidden' : '' }} py-8">
                        <div class="text-3xl" aria-hidden="true">📷</div>
                        <div class="mt-1 text-sm font-bold">Foto Utama Produk</div>
                        <div class="text-[11px] text-forest-500">JPG, PNG, atau WEBP · maks. 2 MB</div>
                    </div>
                    <span class="mt-2 block text-xs font-bold text-harvest-600">{{ $product->mainImageUrl() ? 'Ganti foto' : 'Pilih dari kamera atau galeri' }}</span>
                    <input type="file" name="main_image" accept="image/jpeg,image/png,image/webp" class="sr-only" data-preview="#main-preview" data-preview-hide="#main-placeholder">
                </label>
                @error('main_image')
                    <p class="mt-1 text-xs font-semibold text-clay-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="card p-4">
                <div class="form-label">Foto Tambahan (opsional, maks. {{ ProductRequest::MAX_GALLERY_IMAGES }})</div>
                @if ($editing && $product->images->isNotEmpty())
                    <div class="mt-2 grid grid-cols-4 gap-2">
                        @foreach ($product->images as $image)
                            <label class="relative block">
                                <img src="{{ $image->url() }}" alt="" class="aspect-square w-full rounded-lg object-cover">
                                <span class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1 rounded-b-lg bg-forest-700/80 py-0.5 text-[10px] font-bold text-cream">
                                    <input type="checkbox" name="remove_gallery[]" value="{{ $image->id }}" class="h-3 w-3 rounded border-0 text-clay-500"> hapus
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
                <input type="file" name="gallery[]" multiple accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-xs file:mr-3 file:rounded-full file:border-0 file:bg-forest-700 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-cream">
                @error('gallery')
                    <p class="mt-1 text-xs font-semibold text-clay-600">{{ $message }}</p>
                @enderror
                @error('gallery.*')
                    <p class="mt-1 text-xs font-semibold text-clay-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Fields --}}
        <div class="space-y-4 lg:col-span-3">
            <x-field name="name" label="Nama Produk">
                <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="150" class="form-input" placeholder="cth. Beras Merah Lokal">
            </x-field>

            <div>
                <span class="form-label">Kategori</span>
                <div class="mt-1 grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach ($categories as $category)
                        <label>
                            <input type="radio" name="category_id" value="{{ $category->id }}" class="peer sr-only" @checked((int) old('category_id', $product->category_id) === $category->id) required>
                            <div class="cursor-pointer rounded-xl border border-forest-200 bg-white py-2.5 text-center text-xs font-bold peer-checked:border-forest-700 peer-checked:bg-forest-700 peer-checked:text-cream peer-focus-visible:ring-2 peer-focus-visible:ring-harvest-300">{{ $category->icon }} {{ $category->name }}</div>
                        </label>
                    @endforeach
                </div>
                @error('category_id')
                    <p class="mt-1 text-xs font-semibold text-clay-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <x-field name="price" label="Harga (Rp)">
                    <input id="price" name="price" type="number" min="0" step="1" inputmode="numeric" value="{{ old('price', $product->price !== null ? (int) $product->price : null) }}" required class="form-input" placeholder="15000">
                </x-field>
                <x-field name="unit" label="Satuan">
                    <input id="unit" name="unit" value="{{ old('unit', $product->unit) }}" required maxlength="30" class="form-input" placeholder="kg / ikat / pcs">
                </x-field>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <x-field name="stock" label="Stok Tersedia" hint="Kosongkan bila stok tidak dihitung.">
                    <input id="stock" name="stock" type="number" min="0" inputmode="numeric" value="{{ old('stock', $product->stock) }}" class="form-input" placeholder="0">
                </x-field>
                <x-field name="stock_status" label="Ketersediaan">
                    <select id="stock_status" name="stock_status" class="form-input">
                        @foreach (StockStatus::cases() as $stockStatus)
                            <option value="{{ $stockStatus->value }}" @selected(old('stock_status', $product->stock_status?->value) === $stockStatus->value)>{{ $stockStatus->label() }}</option>
                        @endforeach
                    </select>
                </x-field>
            </div>

            <x-field name="description" label="Deskripsi">
                <textarea id="description" name="description" rows="5" required maxlength="5000" class="form-input" placeholder="Hasil panen sendiri, digiling segar sesuai pesanan…">{{ old('description', $product->description) }}</textarea>
            </x-field>

            <div class="flex flex-col gap-2 pt-2 sm:flex-row">
                <button name="action" value="draft" class="btn-outline flex-1 py-3">Simpan sebagai Draf</button>
                <button name="action" value="submit" class="btn-primary flex-1 py-3.5">Kirim ke Admin</button>
            </div>
        </div>
    </form>
@endsection
