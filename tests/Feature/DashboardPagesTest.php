<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Renders every dashboard page with realistic data so view errors surface (acceptance criterion: dashboards work).
 */
class DashboardPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_page_renders(): void
    {
        $this->seed(PageSeeder::class);
        $admin = User::factory()->admin()->create();
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->pending()->for($seller->store)->for(Category::factory())->create();
        $order = Order::query()->forceCreate(['store_id' => $seller->store->id, 'order_code' => 'LD-TEST-1', 'buyer_name' => 'Rina']);
        $banner = Banner::query()->forceCreate(['title' => 'Panen Raya', 'image' => 'banners/a.jpg']);
        ActivityLog::query()->create(['user_id' => $admin->id, 'action' => 'auth.login']);

        $routes = [
            route('admin.dashboard'),
            route('admin.verification.index'),
            route('admin.sellers.index'),
            route('admin.sellers.show', $seller),
            route('admin.categories.index'),
            route('admin.categories.create'),
            route('admin.categories.edit', $product->category),
            route('admin.products.index'),
            route('admin.products.show', $product),
            route('admin.orders.index'),
            route('admin.orders.show', $order),
            route('admin.banners.index'),
            route('admin.banners.create'),
            route('admin.banners.edit', $banner),
            route('admin.pages.index'),
            route('admin.pages.edit', Page::query()->first()),
            route('admin.reports'),
            route('admin.reports.sales'),
            route('admin.reports.sales', ['periode' => 'tahun-ini']),
            route('admin.products.edit', $product),
            route('admin.stores.edit', $seller),
            route('admin.users.index'),
            route('admin.users.create'),
            route('admin.activity'),
            route('admin.settings'),
        ];

        foreach ($routes as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_every_seller_page_renders(): void
    {
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->published()->for($seller->store)->for(Category::factory())->create();
        $order = Order::query()->forceCreate(['store_id' => $seller->store->id, 'order_code' => 'LD-TEST-1', 'buyer_name' => 'Rina']);
        $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'unit_price' => 1000, 'quantity' => 2, 'subtotal' => 2000]);

        $routes = [
            route('seller.dashboard'),
            route('seller.products.index'),
            route('seller.products.create'),
            route('seller.products.edit', $product),
            route('seller.orders.index'),
            route('seller.orders.create'),
            route('seller.orders.show', $order),
            route('seller.store.edit'),
            route('seller.account'),
            route('seller.settings'),
            route('seller.reports.sales'),
        ];

        foreach ($routes as $url) {
            $this->actingAs($seller)->get($url)->assertOk();
        }
    }

    public function test_auth_pages_render_for_guests(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('seller.register'))->assertOk();
        $this->get(route('password.request'))->assertOk();
        $this->get(route('password.reset', 'token'))->assertOk();
    }
}
