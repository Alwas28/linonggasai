@extends('layouts.seller')

@section('title', 'Laporan Penjualan')

@section('content')
    <h1 class="font-display text-2xl font-extrabold">Laporan Penjualan</h1>
    <p class="mb-4 text-sm text-forest-500">Ringkasan penjualan toko Anda dari rekap pesanan.</p>

    @include('partials.sales.filter', [
        'action' => route('seller.reports.sales'),
        'exportUrl' => route('seller.reports.sales.export', $report->queryParameters()),
    ])

    @include('partials.sales.summary')

    <div class="mt-4">
        @include('partials.sales.chart', ['title' => 'Penjualan '.$report->label()])
    </div>

    <div class="mt-4">
        @include('partials.sales.breakdown')
    </div>

    @if ($summary['recorded'] === 0)
        <div class="mt-4 rounded-2xl border border-harvest-100 bg-harvest-50 p-4 text-sm">
            Laporan diisi dari <a href="{{ route('seller.orders.create') }}" class="font-bold text-harvest-600">Catat Pesanan</a>. Tandai pesanan <strong>Selesai</strong> agar masuk ke penjualan.
        </div>
    @endif
@endsection
