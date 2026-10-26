@extends('layouts.admin')

@section('title', 'Pesanan '.$order->order_code)

@section('content')
    <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest-700">← Rekap Pesanan</a>
    <div class="mt-2 flex flex-wrap items-center gap-2">
        <x-badge :status="$order->order_status" />
        <x-badge :status="$order->payment_status" />
        <span class="text-xs text-forest-500">Dicatat {{ $order->created_at->translatedFormat('d F Y, H:i') }}</span>
    </div>

    <div class="mt-4 grid gap-4 lg:grid-cols-3">
        <div class="card overflow-hidden lg:col-span-2">
            <table class="w-full text-sm">
                <thead class="bg-forest-50 text-left text-xs uppercase tracking-wide text-forest-500">
                    <tr><th class="px-5 py-3">Produk</th><th class="px-5 py-3 text-right">Harga</th><th class="px-5 py-3 text-right">Jumlah</th><th class="px-5 py-3 text-right">Subtotal</th></tr>
                </thead>
                <tbody class="divide-y divide-forest-100">
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="px-5 py-3 font-semibold">{{ $item->product_name }}</td>
                            <td class="px-5 py-3 text-right">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right">{{ $item->quantity }}</td>
                            <td class="px-5 py-3 text-right font-semibold">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot><tr class="border-t border-forest-100"><td colspan="3" class="px-5 py-3 text-right font-bold">Total</td><td class="px-5 py-3 text-right font-display text-lg font-extrabold">{{ $order->formattedTotal() }}</td></tr></tfoot>
            </table>
        </div>
        <div class="card space-y-3 p-5 text-sm">
            <div><div class="text-xs text-forest-400">Pembeli</div><div class="font-semibold">{{ $order->buyer_name }}</div><div class="text-xs text-forest-500">{{ $order->buyer_phone ? \App\Services\WhatsApp::display($order->buyer_phone) : '-' }}</div></div>
            <div><div class="text-xs text-forest-400">Toko</div><a href="{{ route('admin.sellers.show', $order->store->user) }}" class="font-semibold hover:text-harvest-600">{{ $order->store->name }}</a></div>
            <div><div class="text-xs text-forest-400">Catatan</div><div class="whitespace-pre-line">{{ $order->notes ?: '-' }}</div></div>
            <p class="rounded-xl bg-forest-50 p-3 text-xs text-forest-500">Status pesanan dan pembayaran dikelola oleh penjual. Admin hanya melihat rekap.</p>
        </div>
    </div>
@endsection
