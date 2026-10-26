@extends('layouts.admin')

@section('title', 'Rekap Pesanan')

@use('App\Enums\OrderStatus')
@use('App\Enums\PaymentStatus')

@section('content')
    <p class="text-sm text-forest-500">Pesanan dicatat secara opsional oleh penjual sebagai catatan administratif. Status pembayaran dikonfirmasi manual oleh penjual.</p>

    <div class="no-scrollbar mt-4 flex gap-2 overflow-x-auto">
        <a href="{{ route('admin.orders.index', array_filter(['pembayaran' => $activePayment?->value])) }}" class="chip {{ $activeStatus ? '' : 'chip-active' }}">Semua</a>
        @foreach (OrderStatus::cases() as $status)
            <a href="{{ route('admin.orders.index', array_filter(['status' => $status->value, 'pembayaran' => $activePayment?->value])) }}" class="chip {{ $activeStatus === $status ? 'chip-active' : '' }}">{{ $status->label() }}</a>
        @endforeach
    </div>
    <div class="no-scrollbar mt-2 flex gap-2 overflow-x-auto text-xs">
        <span class="self-center font-bold text-forest-400">Pembayaran:</span>
        <a href="{{ route('admin.orders.index', array_filter(['status' => $activeStatus?->value])) }}" class="chip !py-1 !text-xs {{ $activePayment ? '' : 'chip-active' }}">Semua</a>
        @foreach (PaymentStatus::cases() as $payment)
            <a href="{{ route('admin.orders.index', array_filter(['status' => $activeStatus?->value, 'pembayaran' => $payment->value])) }}" class="chip !py-1 !text-xs {{ $activePayment === $payment ? 'chip-active' : '' }}">{{ $payment->label() }}</a>
        @endforeach
    </div>

    @if ($orders->isEmpty())
        <x-empty-state class="mt-5" icon="🧾" title="Belum ada pesanan tercatat" />
    @else
        <div class="card mt-5 hidden overflow-hidden md:block">
            <table class="w-full text-sm">
                <thead class="bg-forest-50 text-left text-xs uppercase tracking-wide text-forest-500">
                    <tr><th class="px-5 py-3">Kode</th><th class="px-5 py-3">Pembeli</th><th class="px-5 py-3">Produk</th><th class="px-5 py-3">Toko</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Pembayaran</th></tr>
                </thead>
                <tbody class="divide-y divide-forest-100">
                    @foreach ($orders as $order)
                        <tr class="hover:bg-cream">
                            <td class="px-5 py-3 font-bold"><a href="{{ route('admin.orders.show', $order) }}" class="hover:text-harvest-600">{{ $order->order_code }}</a><div class="text-[11px] font-normal text-forest-400">{{ $order->created_at->translatedFormat('d M Y') }}</div></td>
                            <td class="px-5 py-3">{{ $order->buyer_name }}</td>
                            <td class="px-5 py-3">{{ $order->items->first()?->product_name }}{{ $order->items->count() > 1 ? ' +'.($order->items->count() - 1) : '' }}</td>
                            <td class="px-5 py-3 text-forest-500">{{ $order->store->name }}</td>
                            <td class="px-5 py-3 font-bold">{{ $order->formattedTotal() }}</td>
                            <td class="px-5 py-3"><x-badge :status="$order->order_status" /></td>
                            <td class="px-5 py-3"><x-badge :status="$order->payment_status" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-5 space-y-3 md:hidden">
            @foreach ($orders as $order)
                <a href="{{ route('admin.orders.show', $order) }}" class="card block p-4">
                    <div class="flex items-center justify-between"><span class="text-xs font-bold text-forest-500">{{ $order->order_code }}</span><x-badge :status="$order->order_status" /></div>
                    <div class="mt-2 font-display font-bold">{{ $order->items->first()?->product_name }}</div>
                    <div class="mt-0.5 text-xs text-forest-500">{{ $order->buyer_name }} → dari {{ $order->store->name }}</div>
                    <div class="mt-3 flex items-center justify-between border-t border-forest-100 pt-3">
                        <span class="font-bold">{{ $order->formattedTotal() }}</span>
                        <x-badge :status="$order->payment_status" />
                    </div>
                </a>
            @endforeach
        </div>
        {{ $orders->links() }}
    @endif
@endsection
