<?php

namespace Tests\Feature\Public;

use App\Enums\ProductStatus;
use App\Enums\VerificationStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private function publishedProduct(array $attributes = [], VerificationStatus $sellerStatus = VerificationStatus::Approved): Product
    {
        $seller = User::factory()->seller($sellerStatus)->create();

        $category = Category::query()->where('slug', 'pertanian')->first()
            ?? Category::factory()->create(['slug' => 'pertanian', 'name' => 'Pertanian']);

        return Product::factory()
            ->published()
            ->for($seller->store)
            ->for($category)
            ->create($attributes);
    }

    public function test_guest_can_browse_home_catalog_category_product_and_store_pages(): void
    {
        $this->seed(PageSeeder::class);
        $product = $this->publishedProduct(['name' => 'Beras Lokal']);

        $this->get(route('home'))->assertOk()->assertSee('Beras Lokal');
        $this->get(route('products.index'))->assertOk()->assertSee('Beras Lokal');
        $this->get(route('products.index', ['kategori' => 'pertanian']))->assertOk()->assertSee('Beras Lokal');
        $this->get(route('categories.show', $product->category))->assertOk()->assertSee('Beras Lokal');
        $this->get(route('products.show', $product))->assertOk()->assertSee('Beras Lokal');
        $this->get(route('stores.show', $product->store))->assertOk()->assertSee('Beras Lokal');

        foreach (['pages.about', 'pages.how-to-order', 'pages.location', 'pages.contact'] as $page) {
            $this->get(route($page))->assertOk();
        }
    }

    public function test_catalog_search_filters_products_by_name(): void
    {
        $this->publishedProduct(['name' => 'Ikan Lele']);
        $this->publishedProduct(['name' => 'Kerupuk Udang']);

        $this->get(route('products.index', ['q' => 'lele']))
            ->assertOk()
            ->assertSee('Ikan Lele')
            ->assertDontSee('Kerupuk Udang');
    }

    public function test_pending_product_is_hidden_from_catalog_and_returns_404(): void
    {
        $product = $this->publishedProduct(['name' => 'Produk Menunggu']);
        $product->forceFill(['status' => ProductStatus::Pending])->save();

        $this->get(route('products.index'))->assertDontSee('Produk Menunggu');
        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_product_of_suspended_seller_is_hidden(): void
    {
        $product = $this->publishedProduct(['name' => 'Produk Penjual Nonaktif'], VerificationStatus::Suspended);

        $this->get(route('products.index'))->assertDontSee('Produk Penjual Nonaktif');
        $this->get(route('products.show', $product))->assertNotFound();
        $this->get(route('stores.show', $product->store))->assertNotFound();
    }

    public function test_product_of_closed_store_is_hidden(): void
    {
        $product = $this->publishedProduct(['name' => 'Produk Toko Tutup']);
        $product->store->forceFill(['status' => 'inactive'])->save();

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_product_in_inactive_category_is_hidden(): void
    {
        $product = $this->publishedProduct(['name' => 'Produk Kategori Nonaktif']);
        $product->category->forceFill(['status' => 'inactive'])->save();

        $this->get(route('products.show', $product))->assertNotFound();
        $this->get(route('categories.show', $product->category))->assertNotFound();
    }

    public function test_whatsapp_button_opens_seller_number_with_prepared_message(): void
    {
        $product = $this->publishedProduct(['name' => 'Beras Merah', 'price' => 18000, 'unit' => 'kg']);
        $product->store->forceFill(['whatsapp_number' => '6281234567890', 'name' => 'Toko Hasan'])->save();

        $response = $this->get(route('products.show', $product))->assertOk();

        $response->assertSee('https://wa.me/6281234567890?text=', false);
        $this->assertStringContainsString(rawurlencode('Nama produk: Beras Merah'), $product->fresh()->whatsappUrl());
        $this->assertStringContainsString(rawurlencode('Toko: Toko Hasan'), $product->fresh()->whatsappUrl());
        $this->assertStringContainsString(rawurlencode('Harga yang tercantum: Rp 18.000 / kg'), $product->fresh()->whatsappUrl());
    }
}
