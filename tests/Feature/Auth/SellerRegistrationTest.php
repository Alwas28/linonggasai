<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Andi Saputra',
            'email' => 'andi@example.com',
            'phone' => '0812-3456-7890',
            'password' => 'rahasia-123',
            'password_confirmation' => 'rahasia-123',
            'store_name' => 'Madu Hutan Andi',
            'address' => 'Jl. Poros Desa',
            'village' => 'Dusun I',
            'terms' => '1',
            ...$overrides,
        ];
    }

    public function test_resident_registers_as_pending_seller_with_store_and_is_logged_in(): void
    {
        $this->post(route('seller.register'), $this->payload())
            ->assertRedirect(route('seller.dashboard'));

        $user = User::query()->where('email', 'andi@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame(UserRole::Seller, $user->role);
        $this->assertSame(VerificationStatus::Pending, $user->sellerProfile->verification_status);
        $this->assertSame('Madu Hutan Andi', $user->store->name);
        $this->assertSame('6281234567890', $user->store->whatsapp_number);
    }

    public function test_registration_ignores_injected_role_and_verification_fields(): void
    {
        $this->post(route('seller.register'), $this->payload([
            'role' => 'admin',
            'verification_status' => 'approved',
        ]));

        $user = User::query()->where('email', 'andi@example.com')->firstOrFail();

        $this->assertSame(UserRole::Seller, $user->role);
        $this->assertSame(VerificationStatus::Pending, $user->sellerProfile->verification_status);
    }

    public function test_registration_requires_fields_and_terms(): void
    {
        $this->post(route('seller.register'), [])
            ->assertSessionHasErrors(['name', 'email', 'phone', 'password', 'store_name', 'address', 'village', 'terms']);

        $this->assertGuest();
    }

    public function test_registration_rejects_invalid_whatsapp_number(): void
    {
        $this->post(route('seller.register'), $this->payload(['phone' => '12345']))
            ->assertSessionHasErrors(['phone' => 'Kolom nomor HP/WhatsApp harus berupa nomor HP/WhatsApp Indonesia yang valid, contoh 081234567890.']);
    }

    public function test_registration_is_refused_when_closed_by_admin(): void
    {
        Setting::put(['accept_seller_registration' => '0']);

        $this->get(route('seller.register'))->assertOk()->assertSee('Pendaftaran penjual sedang ditutup');
        $this->post(route('seller.register'), $this->payload())->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'andi@example.com']);
    }

    public function test_pending_seller_cannot_open_product_management(): void
    {
        $seller = User::factory()->seller(VerificationStatus::Pending)->create();

        $this->actingAs($seller)
            ->get(route('seller.products.create'))
            ->assertRedirect(route('seller.dashboard'));

        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('Pendaftaran sedang diperiksa');
    }
}
