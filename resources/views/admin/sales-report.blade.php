@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
    @include('admin.partials.report-tabs')

    @include('partials.sales.filter', [
        'action' => route('admin.reports.sales'),
        'exportUrl' => route('admin.reports.sales.export', [...$report->queryParameters(), ...array_filter(['toko' => $report->storeId])]),
    ])

    @include('partials.sales.summary')

    <div class="mt-4">
        @include('partials.sales.chart', ['title' => 'Penjualan '.$report->label()])
    </div>

    <div class="mt-4">
        @include('partials.sales.breakdown', ['showStore' => true])
    </div>

    <div class="card mt-4 overflow-hidden">
        <div class="px-5 pt-4 font-display font-bold">Penjualan per Toko</div>
        @if ($stores->isEmpty())
            <p class="px-5 py-4 text-sm text-forest-500">Belum ada data.</p>
        @else
            <div class="overflow-x-auto">
                <table class="mt-3 w-full text-sm">
                    <thead class="bg-forest-50 text-left text-xs uppercase tracking-wide text-forest-500">
                        <tr><th class="px-5 py-3">Toko</th><th class="px-5 py-3 text-right">Pesanan Selesai</th><th class="px-5 py-3 text-right">Penjualan</th><th class="px-5 py-3 text-right">Porsi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-forest-100">
                        @foreach ($stores as $store)
                            <tr>
                                <td class="px-5 py-3 font-semibold"><a href="{{ route('admin.sellers.show', $store->user_id) }}" class="hover:text-harvest-600">{{ $store->name }}</a></td>
                                <td class="px-5 py-3 text-right">{{ $store->orders }}</td>
                                <td class="px-5 py-3 text-right font-bold">Rp {{ number_format((float) $store->revenue, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right text-forest-500">{{ $summary['revenue'] > 0 ? number_format((float) $store->revenue / $summary['revenue'] * 100, 1, ',', '.') : 0 }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
