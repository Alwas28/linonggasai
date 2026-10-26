<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\SellerProfile;
use App\Services\SalesReport;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $sellerCounts = SellerProfile::query()->toBase()
            ->selectRaw('verification_status, count(*) as total')
            ->groupBy('verification_status')
            ->pluck('total', 'verification_status');

        $productCounts = Product::query()->toBase()
            ->whereNull('deleted_at')
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $categories = Category::query()
            ->ordered()
            ->withCount(['products' => fn (Builder $query) => $query->where('status', ProductStatus::Published)])
            ->get();

        $today = CarbonImmutable::today();
        $thisMonth = new SalesReport($today->startOfMonth(), $today->endOfDay(), 'bulan-ini');
        $lastMonth = new SalesReport($today->subMonthNoOverflow()->startOfMonth(), $today->subMonthNoOverflow()->endOfMonth()->endOfDay(), 'bulan-lalu');
        $lastSevenDays = new SalesReport($today->subDays(6), $today->endOfDay(), '7-hari');

        return view('admin.dashboard', [
            'monthSales' => $thisMonth->summary(),
            'lastMonthRevenue' => $lastMonth->summary()['revenue'],
            'weekTimeline' => $lastSevenDays->timeline(),
            'monthCategories' => $thisMonth->byCategory(),
            'sellerCounts' => $sellerCounts,
            'productCounts' => $productCounts,
            'categories' => $categories,
            'publishedTotal' => max(1, (int) $categories->sum('products_count')),
            'orderCount' => Order::query()->count(),
            'pendingSellers' => SellerProfile::query()
                ->with('user.store')
                ->where('verification_status', VerificationStatus::Pending)
                ->oldest()
                ->take(4)
                ->get(),
            'pendingProducts' => Product::query()
                ->with(['store', 'category'])
                ->where('status', ProductStatus::Pending)
                ->oldest('updated_at')
                ->take(5)
                ->get(),
            'activities' => ActivityLog::query()->with('user')->latest('created_at')->take(6)->get(),
        ]);
    }
}
