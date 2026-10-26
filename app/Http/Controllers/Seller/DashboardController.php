<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load(['sellerProfile', 'store']);
        $store = $user->store;

        $statusCounts = $store
            ? $store->products()->toBase()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')
            : collect();

        $activeOrders = $store
            ? $store->orders()
                ->with('items')
                ->whereNotIn('order_status', [OrderStatus::Completed, OrderStatus::Cancelled])
                ->latest()
                ->take(3)
                ->get()
            : collect();

        $lowStock = $store
            ? $store->products()
                ->where('status', ProductStatus::Published)
                ->whereNotNull('stock')
                ->where('stock', '<', 10)
                ->orderBy('stock')
                ->take(3)
                ->get()
            : collect();

        return view('seller.dashboard', [
            'user' => $user,
            'store' => $store,
            'statusCounts' => $statusCounts,
            'activeOrders' => $activeOrders,
            'lowStock' => $lowStock,
            'monthlyRevenue' => $store
                ? (float) $store->orders()
                    ->where('order_status', OrderStatus::Completed)
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('total_amount')
                : 0.0,
            'monthlyCompleted' => $store
                ? $store->orders()
                    ->where('order_status', OrderStatus::Completed)
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count()
                : 0,
        ]);
    }
}
