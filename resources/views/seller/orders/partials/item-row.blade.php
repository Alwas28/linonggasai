<div data-item class="grid grid-cols-12 items-end gap-2 rounded-xl bg-forest-50 p-3">
    <div class="col-span-12 sm:col-span-6">
        <label class="form-label">Produk</label>
        <select data-product data-name="product_id" name="items[{{ $index }}][product_id]" required class="form-input">
            <option value="">— Pilih produk —</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" data-price="{{ (int) $product->price }}" @selected((string) ($item['product_id'] ?? '') === (string) $product->id)>{{ $product->name }} ({{ $product->formattedPrice() }}/{{ $product->unit }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-span-4 sm:col-span-2">
        <label class="form-label">Jumlah</label>
        <input data-qty data-name="quantity" name="items[{{ $index }}][quantity]" type="number" min="1" value="{{ $item['quantity'] ?? 1 }}" required class="form-input">
    </div>
    <div class="col-span-6 sm:col-span-3">
        <label class="form-label">Harga Satuan Disepakati</label>
        <input data-price data-name="unit_price" name="items[{{ $index }}][unit_price]" type="number" min="0" value="{{ $item['unit_price'] ?? '' }}" required class="form-input">
    </div>
    <div class="col-span-2 sm:col-span-1">
        <button type="button" data-remove class="grid h-10 w-full place-items-center rounded-xl text-clay-600 hover:bg-clay-50" aria-label="Hapus baris">✕</button>
    </div>
    @if (is_int($index))
        @foreach (['product_id', 'quantity', 'unit_price'] as $field)
            @error("items.$index.$field")
                <p class="col-span-12 text-xs font-semibold text-clay-600">{{ $message }}</p>
            @enderror
        @endforeach
    @endif
</div>
