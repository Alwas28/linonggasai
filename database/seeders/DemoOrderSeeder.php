<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Demo order records over the last 60 days so the sales report has data.
 * Additive: stores that already have five or more orders are skipped.
 */
class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        $buyers = ['Rina (Unaaha)', 'Warung Makan Sejati', 'Dedi', 'Ibu Marni', 'Koperasi Tani', 'Yusuf', 'Pak Lukman', 'Ibu Ana'];
        mt_srand(2026);

        $stores = Store::query()
            ->whereHas('products', fn ($query) => $query->where('status', ProductStatus::Published))
            ->withCount('orders')
            ->get();

        foreach ($stores as $store) {
            if ($store->orders_count >= 5) {
                continue;
            }

            $products = $store->products()->where('status', ProductStatus::Published)->get();

            foreach (range(1, mt_rand(6, 14)) as $index) {
                $date = Carbon::now()->subDays(mt_rand(0, 59))->setTime(mt_rand(7, 18), mt_rand(0, 59));
                $roll = mt_rand(1, 10);
                $status = match (true) {
                    $roll <= 7 => OrderStatus::Completed,
                    $roll === 8 => OrderStatus::Cancelled,
                    default => OrderStatus::Processing,
                };
                $payment = match ($status) {
                    OrderStatus::Completed => mt_rand(1, 5) === 1 ? PaymentStatus::ReportedPaid : PaymentStatus::ConfirmedPaid,
                    OrderStatus::Cancelled => PaymentStatus::Failed,
                    default => PaymentStatus::Unconfirmed,
                };

                $order = new Order([
                    'buyer_name' => $buyers[array_rand($buyers)],
                    'order_status' => $status->value,
                    'payment_status' => $payment->value,
                ]);
                $order->store()->associate($store);
                $order->created_at = $date;
                $order->updated_at = $date;
                $order->save();

                $total = 0;
                foreach ($products->random(min($products->count(), mt_rand(1, 2))) as $product) {
                    $quantity = mt_rand(1, 10);
                    $subtotal = $quantity * (float) $product->price;
                    $total += $subtotal;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $product->price,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                    ]);
                }

                $order->forceFill(['total_amount' => $total])->saveQuietly();
            }
        }
    }
}
