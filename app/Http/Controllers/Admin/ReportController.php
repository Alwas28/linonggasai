<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

/**
 * Basic reports: sellers, products per category, publication status and recorded orders.
 */
class ReportController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.reports', [
            'sellerCounts' => SellerProfile::query()->toBase()
                ->selectRaw('verification_status, count(*) as total')
                ->groupBy('verification_status')
                ->pluck('total', 'verification_status'),
            'productCounts' => Product::query()->toBase()
                ->whereNull('deleted_at')
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'categories' => Category::query()
                ->ordered()
                ->withCount([
                    'products',
                    'products as published_count' => fn (Builder $query) => $query->where('status', ProductStatus::Published),
                ])
                ->get(),
            'orderStatusCounts' => Order::query()->toBase()
                ->selectRaw('order_status, count(*) as total, sum(total_amount) as amount')
                ->groupBy('order_status')
                ->get()
                ->keyBy('order_status'),
            'paymentStatusCounts' => Order::query()->toBase()
                ->selectRaw('payment_status, count(*) as total')
                ->groupBy('payment_status')
                ->pluck('total', 'payment_status'),
        ]);
    }
}
