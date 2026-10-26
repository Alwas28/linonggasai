<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\OrderRequest;
use App\Models\Order;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Optional order records for deals agreed on WhatsApp (blueprint 4.3 & 5.3).
 */
class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->string('tab', 'aktif')->toString();
        $status = OrderStatus::tryFrom($tab);

        $orders = $request->user()->store->orders()
            ->with('items')
            ->when($tab === 'aktif', fn (Builder $query) => $query->whereNotIn('order_status', [OrderStatus::Completed, OrderStatus::Cancelled]))
            ->when($status, fn (Builder $query) => $query->where('order_status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('seller.orders.index', [
            'orders' => $orders,
            'tab' => $status ? $status->value : ($tab === 'semua' ? 'semua' : 'aktif'),
        ]);
    }

    public function create(Request $request): View
    {
        return view('seller.orders.create', [
            'products' => $request->user()->store->products()->orderBy('name')->get(['id', 'name', 'price', 'unit']),
        ]);
    }

    public function store(OrderRequest $request): RedirectResponse
    {
        $store = $request->user()->store;
        $products = $store->products()->whereKey(collect($request->validated('items'))->pluck('product_id'))->get()->keyBy('id');

        $order = DB::transaction(function () use ($request, $store, $products): Order {
            $order = new Order($request->safe()->only(['buyer_name', 'buyer_phone', 'notes', 'order_status', 'payment_status']));
            $order->store()->associate($store);
            $order->save();

            $total = 0;

            foreach ($request->validated('items') as $item) {
                $product = $products[$item['product_id']];
                $subtotal = round((float) $item['unit_price'] * (int) $item['quantity'], 2);
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);
            }

            $order->forceFill(['total_amount' => $total])->save();

            return $order;
        });

        ActivityLogger::log('order.created', $order, ['code' => $order->order_code]);

        return redirect()->route('seller.orders.show', $order)->with('success', 'Pesanan '.$order->order_code.' dicatat.');
    }

    public function show(Order $order): View
    {
        Gate::authorize('update', $order);

        return view('seller.orders.show', [
            'order' => $order->load('items'),
        ]);
    }

    /**
     * Payment is confirmed manually by the seller after checking the funds were received.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('update', $order);

        $data = $request->validate([
            'order_status' => ['required', Rule::enum(OrderStatus::class)],
            'payment_status' => ['required', Rule::enum(PaymentStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update($data);
        ActivityLogger::log('order.updated', $order, $data);

        return back()->with('success', 'Status pesanan '.$order->order_code.' diperbarui.');
    }
}
