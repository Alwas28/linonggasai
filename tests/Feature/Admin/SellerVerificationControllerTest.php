<?php

namespace Tests\Feature\Admin;

use App\Enums\VerificationStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_approves_pending_seller_and_decision_is_logged(): void
    {
        $admin = User::factory()->admin()->create();
        $seller = User::factory()->seller(VerificationStatus::Pending)->create();

        $this->actingAs($admin)
            ->patch(route('admin.verification.approve', $seller->sellerProfile))
            ->assertRedirect();

        $profile = $seller->sellerProfile->fresh();
        $this->assertSame(VerificationStatus::Approved, $profile->verification_status);
        $this->assertSame($admin->id, $profile->verified_by);
        $this->assertDatabaseHas('activity_logs', ['action' => 'seller.approved', 'user_id' => $admin->id]);
    }

    public function test_rejection_requires_a_reason_and_stores_it(): void
    {
        $admin = User::factory()->admin()->create();
        $seller = User::factory()->seller(VerificationStatus::Pending)->create();

        $this->actingAs($admin)
            ->patch(route('admin.verification.reject', $seller->sellerProfile), ['note' => ''])
            ->assertSessionHasErrors(['note' => 'Alasan penolakan wajib diisi.']);

        $this->actingAs($admin)
            ->patch(route('admin.verification.reject', $seller->sellerProfile), ['note' => 'Alamat tidak lengkap.'])
            ->assertRedirect();

        $profile = $seller->sellerProfile->fresh();
        $this->assertSame(VerificationStatus::Rejected, $profile->verification_status);
        $this->assertSame('Alamat tidak lengkap.', $profile->verification_note);
    }

    public function test_rejected_seller_can_resubmit_corrected_data(): void
    {
        $seller = User::factory()->seller(VerificationStatus::Rejected)->create();

        $this->actingAs($seller)->put(route('seller.account.update'), [
            'name' => $seller->name,
            'email' => $seller->email,
            'phone' => '081234567890',
            'address' => 'Jl. Poros Desa No. 5',
            'village' => 'Dusun II',
        ])->assertRedirect();

        $this->assertSame(VerificationStatus::Pending, $seller->sellerProfile->fresh()->verification_status);
    }

    public function test_seller_cannot_approve_registrations(): void
    {
        $seller = User::factory()->seller(VerificationStatus::Pending)->create();

        $this->actingAs(User::factory()->seller()->create())
            ->patch(route('admin.verification.approve', $seller->sellerProfile))
            ->assertForbidden();

        $this->assertSame(VerificationStatus::Pending, $seller->sellerProfile->fresh()->verification_status);
    }

    public function test_suspending_seller_hides_their_products_until_reactivated(): void
    {
        $admin = User::factory()->admin()->create();
        $seller = User::factory()->seller()->create();
        $product = Product::factory()->published()->for($seller->store)->for(Category::factory())->create();

        $this->actingAs($admin)
            ->patch(route('admin.sellers.suspend', $seller), ['note' => 'Pelanggaran ketentuan.'])
            ->assertRedirect();

        $this->get(route('products.show', $product))->assertNotFound();
        $this->actingAs($seller)->get(route('seller.products.index'))->assertRedirect(route('seller.dashboard'));

        $this->actingAs($admin)->patch(route('admin.sellers.reactivate', $seller))->assertRedirect();

        $this->get(route('products.show', $product))->assertOk();
    }
}
