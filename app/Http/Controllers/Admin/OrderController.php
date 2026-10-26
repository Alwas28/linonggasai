<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only overview of order records kept by sellers.
 */
class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = OrderStatus::tryFrom($request->string('status')->toString());
        $payment = PaymentStatus::tryFrom($request->string('pembayaran')->toString());

        $orders = Order::query()
            ->with(['store', 'items'])
            ->when($status, fn (Builder $query) => $query->where('order_status', $status))
            ->when($payment, fn (Builder $query) => $query->where('payment_status', $payment))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'activeStatus' => $status,
            'activePayment' => $payment,
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load(['store.user', 'items']),
        ]);
    }
}
