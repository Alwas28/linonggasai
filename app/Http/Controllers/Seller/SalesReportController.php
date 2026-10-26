<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\SalesReport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Sales of the signed-in seller's own store only.
 */
class SalesReportController extends Controller
{
    public function index(Request $request): View
    {
        $report = SalesReport::fromRequest($request, $request->user()->store->id);

        return view('seller.sales-report', [
            'report' => $report,
            'summary' => $report->summary(),
            'timeline' => $report->timeline(),
            'products' => $report->byProduct(),
            'categories' => $report->byCategory(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $report = SalesReport::fromRequest($request, $request->user()->store->id);

        return $report->downloadCsv('penjualan-'.$request->user()->store->slug.'-'.$report->from->format('Ymd').'-'.$report->to->format('Ymd').'.csv');
    }
}
