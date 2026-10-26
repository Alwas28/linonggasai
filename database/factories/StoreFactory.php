<?php

namespace Database\Factories;

use App\Enums\StoreStatus;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => 'Toko '.fake()->unique()->firstName(),
            'description' => fake()->sentence(12),
            'whatsapp_number' => '628'.fake()->numerify('##########'),
            'address' => 'Desa Linonggasai',
            'business_hours' => '08.00 - 17.00 WITA',
            'status' => StoreStatus::Active,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StoreStatus::Inactive,
        ]);
    }
}
