@extends('layouts.seller')

@section('title', 'Catat Pesanan')

@section('content')
    <a href="{{ route('seller.orders.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Rekap Pesanan</a>
    <h1 class="mt-2 font-display text-2xl font-extrabold">Catat Pesanan</h1>
    <p class="text-sm text-forest-500">Catat pesanan yang sudah disepakati dengan pembeli di WhatsApp. Website tidak memproses pembayaran.</p>

    @if ($products->isEmpty())
        <x-empty-state class="mt-6" icon="📦" title="Belum ada produk">
            <a href="{{ route('seller.products.create') }}" class="font-bold text-harvest-600">Tambahkan produk</a> terlebih dahulu.
        </x-empty-state>
    @else
        @php
            $oldItems = old('items', [['product_id' => '', 'quantity' => 1, 'unit_price' => '']]);
        @endphp

        <form method="POST" action="{{ route('seller.orders.store') }}" class="mt-5 space-y-5">
            @csrf
            <div class="card space-y-4 p-5">
                <h2 class="font-display font-bold">Pembeli</h2>
                <div class="grid gap-3 sm:grid-cols-2">
                    <x-field name="buyer_name" label="Nama Pembeli">
                        <input id="buyer_name" name="buyer_name" value="{{ old('buyer_name') }}" required maxlength="100" class="form-input">
                    </x-field>
                    <x-field name="buyer_phone" label="No. HP Pembeli (opsional)">
                        <input id="buyer_phone" name="buyer_phone" type="tel" value="{{ old('buyer_phone') }}" maxlength="20" class="form-input">
                    </x-field>
                </div>
            </div>

            <div class="card space-y-3 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="font-display font-bold">Produk</h2>
                    <button type="button" id="add-order-item" class="text-sm font-bold text-harvest-600">+ Tambah baris</button>
                </div>
                @error('items')
                    <p class="text-xs font-semibold text-clay-600">{{ $message }}</p>
                @enderror

                <div id="order-items" class="space-y-3">
                    @foreach ($oldItems as $index => $item)
                        @include('seller.orders.partials.item-row', ['index' => $index, 'item' => $item])
                    @endforeach
                </div>
                <template id="order-item-template">
                    @include('seller.orders.partials.item-row', ['index' => '__i__', 'item' => ['product_id' => '', 'quantity' => 1, 'unit_price' => '']])
                </template>

                <div class="flex items-center justify-between border-t border-forest-100 pt-3">
                    <span class="text-sm font-semibold text-forest-500">Total</span>
                    <span id="order-total" class="font-display text-xl font-extrabold">Rp 0</span>
                </div>
            </div>

            <div class="card space-y-4 p-5">
                @include('seller.orders.partials.status-fields', ['order' => null])
                <x-field name="notes" label="Catatan (ongkir, pengambilan, dll.)">
                    <textarea id="notes" name="notes" rows="3" maxlength="1000" class="form-input">{{ old('notes') }}</textarea>
                </x-field>
            </div>

            <button class="btn-primary w-full py-3.5">Simpan Pesanan</button>
        </form>
    @endif
@endsection
