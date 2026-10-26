<?php

namespace Tests\Feature\Admin;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductModerationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function pendingProduct(): Product
    {
        return Product::factory()->pending()->for(User::factory()->seller()->create()->store)->for(Category::factory())->create();
    }

    public function test_admin_approval_publishes_product_to_catalog(): void
    {
        $admin = User::factory()->admin()->create();
        $product = $this->pendingProduct();

        $this->get(route('products.show', $product))->assertNotFound();

        $this->actingAs($admin)->patch(route('admin.products.approve', $product))->assertRedirect();

        $product->refresh();
        $this->assertSame(ProductStatus::Published, $product->status);
        $this->assertSame($admin->id, $product->approved_by);
        $this->assertNotNull($product->approved_at);
        $this->get(route('products.show', $product))->assertOk();
    }

    public function test_rejection_requires_reason_shown_to_seller(): void
    {
        $admin = User::factory()->admin()->create();
        $product = $this->pendingProduct();

        $this->actingAs($admin)
            ->patch(route('admin.products.reject', $product), [])
            ->assertSessionHasErrors('note');

        $this->actingAs($admin)
            ->patch(route('admin.products.reject', $product), ['note' => 'Foto kurang jelas.'])
            ->assertRedirect();

        $this->assertSame(ProductStatus::Rejected, $product->fresh()->status);

        $this->actingAs($product->store->user)
            ->get(route('seller.products.index'))
            ->assertSee('Foto kurang jelas.');
    }

    public function test_draft_product_cannot_be_approved(): void
    {
        $product = Product::factory()->for(User::factory()->seller()->create()->store)->for(Category::factory())->create();

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.products.approve', $product))
            ->assertForbidden();

        $this->assertSame(ProductStatus::Draft, $product->fresh()->status);
    }

    public function test_seller_cannot_moderate_products(): void
    {
        $product = $this->pendingProduct();

        $this->actingAs($product->store->user)
            ->patch(route('admin.products.approve', $product))
            ->assertForbidden();

        $this->assertSame(ProductStatus::Pending, $product->fresh()->status);
    }
}
