<?php

namespace Tests\Feature\Seller;

use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_records_order_with_total_and_product_snapshot(): void
    {
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->published()->for($seller->store)->for(Category::factory())->create(['name' => 'Beras Lokal', 'price' => 14000]);

        $this->actingAs($seller)->post(route('seller.orders.store'), [
            'buyer_name' => 'Rina',
            'order_status' => 'new',
            'payment_status' => 'unconfirmed',
            'items' => [['product_id' => $product->id, 'quantity' => 10, 'unit_price' => 13500]],
        ])->assertRedirect();

        $order = Order::query()->with('items')->firstOrFail();

        $this->assertSame('135000.00', $order->total_amount);
        $this->assertSame('Beras Lokal', $order->items->first()->product_name);

        $product->update(['name' => 'Beras Lokal Baru']);
        $this->assertSame('Beras Lokal', $order->items->first()->fresh()->product_name);
    }

    public function test_seller_cannot_record_another_stores_product(): void
    {
        $seller = User::factory()->seller()->create();
        $foreign = Product::factory()->published()->for(User::factory()->seller()->create()->store)->for(Category::factory())->create();

        $this->actingAs($seller)->post(route('seller.orders.store'), [
            'buyer_name' => 'Rina',
            'order_status' => 'new',
            'payment_status' => 'unconfirmed',
            'items' => [['product_id' => $foreign->id, 'quantity' => 1, 'unit_price' => 1000]],
        ])->assertSessionHasErrors('items.0.product_id');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_seller_confirms_payment_manually(): void
    {
        $seller = User::factory()->seller()->create();
        $order = Order::query()->forceCreate(['store_id' => $seller->store->id, 'order_code' => 'LD-TEST-1', 'buyer_name' => 'Rina']);

        $this->actingAs($seller)
            ->patch(route('seller.orders.update', $order), ['order_status' => 'completed', 'payment_status' => 'confirmed_paid'])
            ->assertRedirect();

        $this->assertSame(PaymentStatus::ConfirmedPaid, $order->fresh()->payment_status);
    }

    public function test_seller_cannot_view_or_update_another_stores_order(): void
    {
        $seller = User::factory()->seller()->create();
        $other = User::factory()->seller()->create();
        $order = Order::query()->forceCreate(['store_id' => $other->store->id, 'order_code' => 'LD-TEST-2', 'buyer_name' => 'Budi']);

        $this->actingAs($seller)->get(route('seller.orders.show', $order))->assertNotFound();
        $this->actingAs($seller)
            ->patch(route('seller.orders.update', $order), ['order_status' => 'cancelled', 'payment_status' => 'failed'])
            ->assertNotFound();
    }
}
