<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Enums\StockStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'category_id' => Category::factory(),
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'description' => fake()->sentence(15),
            'price' => fake()->numberBetween(5, 200) * 1000,
            'unit' => fake()->randomElement(['kg', 'ikat', 'pcs', 'bungkus']),
            'stock' => fake()->numberBetween(5, 100),
            'stock_status' => StockStatus::Available,
            'status' => ProductStatus::Draft,
        ];
    }

    public function status(ProductStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
            'approved_at' => $status === ProductStatus::Published ? now() : null,
        ]);
    }

    public function published(): static
    {
        return $this->status(ProductStatus::Published);
    }

    public function pending(): static
    {
        return $this->status(ProductStatus::Pending);
    }
}
