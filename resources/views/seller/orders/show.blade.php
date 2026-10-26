@extends('layouts.seller')

@section('title', 'Pesanan '.$order->order_code)

@section('content')
    <a href="{{ route('seller.orders.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Rekap Pesanan</a>
    <div class="mt-2 flex flex-wrap items-center gap-3">
        <h1 class="font-display text-2xl font-extrabold">{{ $order->order_code }}</h1>
        <x-badge :status="$order->order_status" />
        <x-badge :status="$order->payment_status" />
    </div>
    <p class="text-sm text-forest-500">Dicatat {{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>

    <div class="mt-5 grid gap-4 lg:grid-cols-5">
        <div class="space-y-4 lg:col-span-3">
            <div class="card p-5">
                <h2 class="font-display font-bold">Pembeli</h2>
                <div class="mt-2 text-sm">{{ $order->buyer_name }}</div>
                @if ($order->buyer_phone)
                    <a href="{{ \App\Services\WhatsApp::link($order->buyer_phone, 'Halo '.$order->buyer_name.', terkait pesanan '.$order->order_code.' di L-DCH Linonggasai.') }}" target="_blank" rel="noopener" class="mt-1 inline-flex text-sm font-bold text-harvest-600">💬 {{ \App\Services\WhatsApp::display($order->buyer_phone) }}</a>
                @endif
            </div>

            <div class="card overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-forest-50 text-left text-xs uppercase tracking-wide text-forest-500">
                        <tr><th class="px-4 py-3">Produk</th><th class="px-4 py-3 text-right">Jumlah</th><th class="px-4 py-3 text-right">Subtotal</th></tr>
                    </thead>
                    <tbody class="divide-y divide-forest-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-3"><div class="font-semibold">{{ $item->product_name }}</div><div class="text-xs text-forest-500">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</div></td>
                                <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-forest-100"><td colspan="2" class="px-4 py-3 text-right font-bold">Total</td><td class="px-4 py-3 text-right font-display text-lg font-extrabold">{{ $order->formattedTotal() }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <form method="POST" action="{{ route('seller.orders.update', $order) }}" class="card space-y-4 p-5 lg:col-span-2">
            @csrf @method('PATCH')
            <h2 class="font-display font-bold">Perbarui Status</h2>
            @include('seller.orders.partials.status-fields', ['order' => $order])
            <x-field name="notes" label="Catatan">
                <textarea id="notes" name="notes" rows="3" maxlength="1000" class="form-input">{{ old('notes', $order->notes) }}</textarea>
            </x-field>
            <button class="btn-dark w-full">Simpan Perubahan</button>
        </form>
    </div>
@endsection
