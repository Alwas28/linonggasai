@extends('layouts.admin')

@section('title', 'Laporan Penjual & Produk')

@use('App\Enums\OrderStatus')
@use('App\Enums\PaymentStatus')
@use('App\Enums\ProductStatus')
@use('App\Enums\VerificationStatus')

@section('content')
    @include('admin.partials.report-tabs')

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="font-display font-bold">Rekap Penjual</h2>
            <div class="mt-4 grid grid-cols-2 gap-3">
                @foreach (VerificationStatus::cases() as $status)
                    <div class="rounded-xl bg-forest-50 p-3">
                        <div class="text-xs font-semibold text-forest-500">{{ $status->label() }}</div>
                        <div class="font-display text-2xl font-extrabold">{{ $sellerCounts[$status->value] ?? 0 }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card p-5">
            <h2 class="font-display font-bold">Status Publikasi Produk</h2>
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach (ProductStatus::cases() as $status)
                    <div class="rounded-xl bg-forest-50 p-3">
                        <div class="text-xs font-semibold text-forest-500">{{ $status->label() }}</div>
                        <div class="font-display text-2xl font-extrabold">{{ $productCounts[$status->value] ?? 0 }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card overflow-hidden lg:col-span-2">
            <div class="px-5 pt-4 font-display font-bold">Produk per Kategori</div>
            <div class="overflow-x-auto">
                <table class="mt-3 w-full text-sm">
                    <thead class="bg-forest-50 text-left text-xs uppercase tracking-wide text-forest-500">
                        <tr><th class="px-5 py-3">Kategori</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Total Produk</th><th class="px-5 py-3 text-right">Tayang</th></tr>
                    </thead>
                    <tbody class="divide-y divide-forest-100">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="px-5 py-3 font-semibold">{{ $category->icon }} {{ $category->name }}</td>
                                <td class="px-5 py-3"><x-badge :status="$category->status" /></td>
                                <td class="px-5 py-3 text-right">{{ $category->products_count }}</td>
                                <td class="px-5 py-3 text-right font-bold">{{ $category->published_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-5 pt-4 font-display font-bold">Pesanan Tercatat per Status</div>
            <table class="mt-3 w-full text-sm">
                <tbody class="divide-y divide-forest-100">
                    @foreach (OrderStatus::cases() as $status)
                        @php($row = $orderStatusCounts[$status->value] ?? null)
                        <tr>
                            <td class="px-5 py-2.5"><x-badge :status="$status" /></td>
                            <td class="px-5 py-2.5 text-right">{{ $row->total ?? 0 }} pesanan</td>
                            <td class="px-5 py-2.5 text-right font-bold">Rp {{ number_format((float) ($row->amount ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card p-5">
            <h2 class="font-display font-bold">Status Pembayaran (dicatat penjual)</h2>
            <div class="mt-4 space-y-2 text-sm">
                @foreach (PaymentStatus::cases() as $status)
                    <div class="flex items-center justify-between"><x-badge :status="$status" /><span class="font-bold">{{ $paymentStatusCounts[$status->value] ?? 0 }}</span></div>
                @endforeach
            </div>
            <p class="mt-4 text-xs text-forest-400">Website tidak memproses pembayaran. Angka ini hanya berasal dari rekap yang dicatat penjual.</p>
        </div>
    </div>
@endsection
