<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SalesReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private function recordOrder(Store $store, Product $product, int $quantity, int $price, OrderStatus $status, PaymentStatus $payment, string $date): Order
    {
        $order = Order::query()->forceCreate([
            'store_id' => $store->id,
            'order_code' => 'LD-'.fake()->unique()->numerify('#####'),
            'buyer_name' => 'Pembeli',
            'order_status' => $status,
            'payment_status' => $payment,
            'total_amount' => $quantity * $price,
            'created_at' => Carbon::parse($date),
            'updated_at' => Carbon::parse($date),
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $price,
            'quantity' => $quantity,
            'subtotal' => $quantity * $price,
        ]);

        return $order;
    }

    public function test_report_sums_only_completed_orders_inside_the_period(): void
    {
        $this->travelTo(Carbon::parse('2026-10-20 10:00'));
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->published()->for($seller->store)->for(Category::factory()->state(['name' => 'Perikanan']))->create(['name' => 'Ikan Lele']);

        $this->recordOrder($seller->store, $product, 10, 28000, OrderStatus::Completed, PaymentStatus::ConfirmedPaid, '2026-10-05');
        $this->recordOrder($seller->store, $product, 5, 28000, OrderStatus::Completed, PaymentStatus::ReportedPaid, '2026-10-06');
        $this->recordOrder($seller->store, $product, 99, 28000, OrderStatus::Cancelled, PaymentStatus::Failed, '2026-10-07');
        $this->recordOrder($seller->store, $product, 99, 28000, OrderStatus::Completed, PaymentStatus::ConfirmedPaid, '2026-09-15');

        $response = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.sales', ['periode' => 'bulan-ini']))
            ->assertOk();

        $response->assertViewHas('summary', fn (array $summary): bool => $summary['revenue'] === 420000.0
            && $summary['orders'] === 2
            && $summary['items'] === 15
            && $summary['confirmed_paid'] === 280000.0
            && $summary['cancelled'] === 1);
        $response->assertSee('Rp 420.000')->assertSee('Ikan Lele')->assertSee('Perikanan');
    }

    public function test_report_can_be_filtered_by_store_and_custom_dates(): void
    {
        $sellerA = User::factory()->seller()->create();
        $sellerB = User::factory()->seller()->create();
        $category = Category::factory()->create();
        $productA = Product::factory()->published()->for($sellerA->store)->for($category)->create();
        $productB = Product::factory()->published()->for($sellerB->store)->for($category)->create();

        $this->recordOrder($sellerA->store, $productA, 1, 10000, OrderStatus::Completed, PaymentStatus::ConfirmedPaid, '2026-08-10');
        $this->recordOrder($sellerB->store, $productB, 1, 50000, OrderStatus::Completed, PaymentStatus::ConfirmedPaid, '2026-08-10');

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.sales', ['periode' => 'kustom', 'dari' => '2026-08-01', 'sampai' => '2026-08-31', 'toko' => $sellerA->store->id]))
            ->assertOk()
            ->assertViewHas('summary', fn (array $summary): bool => $summary['revenue'] === 10000.0);
    }

    public function test_csv_export_lists_completed_order_items(): void
    {
        $this->travelTo(Carbon::parse('2026-10-20 10:00'));
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->published()->for($seller->store)->for(Category::factory())->create(['name' => 'Beras Lokal']);
        $order = $this->recordOrder($seller->store, $product, 3, 14000, OrderStatus::Completed, PaymentStatus::ConfirmedPaid, '2026-10-10');

        $response = $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.sales.export', ['periode' => 'bulan-ini']))
            ->assertOk()
            ->assertDownload();

        $csv = $response->streamedContent();
        $this->assertStringContainsString($order->order_code, $csv);
        $this->assertStringContainsString('Beras Lokal', $csv);
    }

    public function test_seller_sees_only_own_store_sales(): void
    {
        $this->travelTo(Carbon::parse('2026-10-20 10:00'));
        $seller = User::factory()->seller()->create();
        $other = User::factory()->seller()->create();
        $category = Category::factory()->create();
        $mine = Product::factory()->published()->for($seller->store)->for($category)->create(['name' => 'Produk Saya']);
        $theirs = Product::factory()->published()->for($other->store)->for($category)->create(['name' => 'Produk Orang Lain']);

        $this->recordOrder($seller->store, $mine, 2, 10000, OrderStatus::Completed, PaymentStatus::ConfirmedPaid, '2026-10-10');
        $this->recordOrder($other->store, $theirs, 1, 90000, OrderStatus::Completed, PaymentStatus::ConfirmedPaid, '2026-10-10');

        $this->actingAs($seller)
            ->get(route('seller.reports.sales', ['periode' => 'bulan-ini']))
            ->assertOk()
            ->assertViewHas('summary', fn (array $summary): bool => $summary['revenue'] === 20000.0)
            ->assertSee('Produk Saya')
            ->assertDontSee('Produk Orang Lain');
    }

    public function test_seller_cannot_open_admin_sales_report(): void
    {
        $this->actingAs(User::factory()->seller()->create())
            ->get(route('admin.reports.sales'))
            ->assertForbidden();
    }
}
