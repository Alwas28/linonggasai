<?php

namespace Database\Factories;

use App\Enums\VerificationStatus;
use App\Models\SellerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SellerProfile>
 */
class SellerProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'address' => 'Jl. Poros Desa No. '.fake()->numberBetween(1, 99),
            'village' => 'Dusun '.fake()->randomElement(['I', 'II', 'III', 'IV']),
            'phone' => '08'.fake()->numerify('##########'),
            'verification_status' => VerificationStatus::Pending,
        ];
    }

    public function status(VerificationStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => $status,
            'verified_at' => $status === VerificationStatus::Pending ? null : now(),
            'verification_note' => $status === VerificationStatus::Rejected ? 'Data belum lengkap.' : null,
        ]);
    }
}
