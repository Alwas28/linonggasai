<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\SalesReport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Sales across all stores, based on the order records kept by sellers.
 */
class SalesReportController extends Controller
{
    public function index(Request $request): View
    {
        $report = SalesReport::fromRequest($request, $this->storeFilter($request));

        return view('admin.sales-report', [
            'report' => $report,
            'summary' => $report->summary(),
            'timeline' => $report->timeline(),
            'stores' => $report->byStore(),
            'products' => $report->byProduct(),
            'categories' => $report->byCategory(),
            'storeOptions' => Store::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $report = SalesReport::fromRequest($request, $this->storeFilter($request));

        return $report->downloadCsv('laporan-penjualan-'.$report->from->format('Ymd').'-'.$report->to->format('Ymd').'.csv');
    }

    private function storeFilter(Request $request): ?int
    {
        return $request->integer('toko') ?: null;
    }
}
