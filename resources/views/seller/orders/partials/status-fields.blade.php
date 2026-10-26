@use('App\Enums\OrderStatus')
@use('App\Enums\PaymentStatus')

<div class="grid gap-3 sm:grid-cols-2">
    <x-field name="order_status" label="Status Pesanan">
        <select id="order_status" name="order_status" class="form-input">
            @foreach (OrderStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('order_status', $order?->order_status?->value ?? 'new') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </x-field>
    <x-field name="payment_status" label="Status Pembayaran" hint="Pilih status Lunas hanya setelah Anda memeriksa dana benar-benar diterima.">
        <select id="payment_status" name="payment_status" class="form-input">
            @foreach (PaymentStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(old('payment_status', $order?->payment_status?->value ?? 'unconfirmed') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
    </x-field>
</div>
