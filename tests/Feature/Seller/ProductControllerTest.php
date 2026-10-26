<?php

namespace Tests\Feature\Seller;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seller = User::factory()->seller()->create();
        $this->category = Category::factory()->create();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Beras Merah',
            'category_id' => $this->category->id,
            'description' => 'Beras merah lokal hasil panen sendiri.',
            'price' => 18000,
            'unit' => 'kg',
            'stock' => 30,
            'stock_status' => 'available',
            'main_image' => UploadedFile::fake()->image('beras.jpg', 600, 600),
            'action' => 'submit',
            ...$overrides,
        ];
    }

    public function test_submitted_product_waits_for_moderation_with_stored_photo(): void
    {
        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $this->payload())
            ->assertRedirect(route('seller.products.index'));

        $product = Product::query()->firstOrFail();

        $this->assertSame(ProductStatus::Pending, $product->status);
        $this->assertTrue($product->store->is($this->seller->store));
        $this->assertSame('18000.00', $product->price);
        Storage::disk('public')->assertExists($product->main_image);
        $this->assertDatabaseHas('activity_logs', ['action' => 'product.created', 'user_id' => $this->seller->id]);
    }

    public function test_seller_cannot_publish_own_product_by_injecting_status(): void
    {
        $this->actingAs($this->seller)->post(route('seller.products.store'), $this->payload([
            'status' => 'published',
            'approved_by' => $this->seller->id,
        ]));

        $product = Product::query()->firstOrFail();

        $this->assertSame(ProductStatus::Pending, $product->status);
        $this->assertNull($product->approved_by);
    }

    public function test_draft_action_saves_product_as_draft(): void
    {
        $this->actingAs($this->seller)->post(route('seller.products.store'), $this->payload(['action' => 'draft']));

        $this->assertSame(ProductStatus::Draft, Product::query()->firstOrFail()->status);
    }

    public function test_product_requires_name_category_price_unit_description_and_photo(): void
    {
        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), ['action' => 'submit'])
            ->assertSessionHasErrors(['name', 'category_id', 'price', 'unit', 'description', 'main_image', 'stock_status']);
    }

    public function test_negative_price_is_rejected(): void
    {
        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $this->payload(['price' => -1000]))
            ->assertSessionHasErrors(['price' => 'Harga minimal bernilai 0.']);
    }

    public function test_inactive_category_is_rejected(): void
    {
        $inactive = Category::factory()->inactive()->create();

        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $this->payload(['category_id' => $inactive->id]))
            ->assertSessionHasErrors('category_id');
    }

    public function test_non_image_upload_is_rejected(): void
    {
        $this->actingAs($this->seller)
            ->post(route('seller.products.store'), $this->payload(['main_image' => UploadedFile::fake()->create('script.php', 10, 'text/x-php')]))
            ->assertSessionHasErrors('main_image');
    }

    public function test_editing_published_product_sends_it_back_to_moderation(): void
    {
        $product = Product::factory()->published()->for($this->seller->store)->for($this->category)->create(['main_image' => 'products/old.jpg']);

        $this->actingAs($this->seller)
            ->put(route('seller.products.update', $product), $this->payload(['name' => 'Beras Merah Super', 'main_image' => null]))
            ->assertRedirect(route('seller.products.index'));

        $product->refresh();
        $this->assertSame('Beras Merah Super', $product->name);
        $this->assertSame(ProductStatus::Pending, $product->status);
        $this->assertNull($product->approved_at);
    }

    public function test_stock_update_keeps_product_published(): void
    {
        $product = Product::factory()->published()->for($this->seller->store)->for($this->category)->create(['stock' => 10]);

        $this->actingAs($this->seller)
            ->patch(route('seller.products.stock', $product), ['stock' => 4, 'stock_status' => 'available'])
            ->assertRedirect();

        $product->refresh();
        $this->assertSame(4, $product->stock);
        $this->assertSame(ProductStatus::Published, $product->status);
    }

    public function test_seller_cannot_edit_another_sellers_product(): void
    {
        $other = User::factory()->seller()->create();
        $product = Product::factory()->published()->for($other->store)->for($this->category)->create(['name' => 'Milik Orang Lain']);

        $this->actingAs($this->seller)->get(route('seller.products.edit', $product))->assertNotFound();
        $this->actingAs($this->seller)->put(route('seller.products.update', $product), $this->payload())->assertNotFound();
        $this->actingAs($this->seller)->patch(route('seller.products.stock', $product), ['stock' => 0, 'stock_status' => 'available'])->assertNotFound();
        $this->actingAs($this->seller)->delete(route('seller.products.destroy', $product))->assertNotFound();

        $this->assertSame('Milik Orang Lain', $product->fresh()->name);
    }
}
