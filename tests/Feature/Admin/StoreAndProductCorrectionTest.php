<?php

namespace Tests\Feature\Admin;

use App\Enums\ProductStatus;
use App\Enums\StoreStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreAndProductCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_corrects_store_profile_and_change_is_logged(): void
    {
        $admin = User::factory()->admin()->create();
        $seller = User::factory()->seller()->create();

        $this->actingAs($admin)->put(route('admin.stores.update', $seller), [
            'name' => 'Toko Hasan Baru',
            'whatsapp_number' => '0812 9999 8888',
            'status' => 'inactive',
        ])->assertRedirect(route('admin.sellers.show', $seller));

        $store = $seller->store->fresh();
        $this->assertSame('Toko Hasan Baru', $store->name);
        $this->assertSame('6281299998888', $store->whatsapp_number);
        $this->assertSame(StoreStatus::Inactive, $store->status);
        $this->assertDatabaseHas('activity_logs', ['action' => 'store.updated', 'user_id' => $admin->id]);
    }

    public function test_admin_corrects_product_without_changing_publication_status(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->published()->for(User::factory()->seller()->create()->store)->for($category)->create(['main_image' => 'products/a.jpg']);

        $this->actingAs($admin)->put(route('admin.products.update', $product), [
            'name' => 'Beras Lokal (koreksi)',
            'category_id' => $category->id,
            'description' => 'Deskripsi diperbaiki.',
            'price' => 15000,
            'unit' => 'kg',
            'stock_status' => 'available',
        ])->assertRedirect(route('admin.products.show', $product));

        $product->refresh();
        $this->assertSame('Beras Lokal (koreksi)', $product->name);
        $this->assertSame(ProductStatus::Published, $product->status);
    }

    public function test_seller_cannot_use_admin_correction_routes(): void
    {
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->published()->for($seller->store)->for(Category::factory())->create();

        $this->actingAs($seller)->get(route('admin.products.edit', $product))->assertForbidden();
        $this->actingAs($seller)->get(route('admin.stores.edit', $seller))->assertForbidden();
    }
}
