<?php

namespace Tests\Feature\Seller;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_sees_own_activity_and_admin_decisions_but_not_other_sellers(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Rahasia']);
        $seller = User::factory()->seller()->create();
        $other = User::factory()->seller()->create();

        ActivityLogger::log('order.created', $seller->store, ['code' => 'LD-MILIKKU'], $seller);
        ActivityLogger::log('seller.approved', $seller->sellerProfile, [], $admin);
        ActivityLogger::log('order.created', $other->store, ['code' => 'LD-ORANGLAIN'], $other);

        $this->actingAs($seller)
            ->get(route('seller.settings'))
            ->assertOk()
            ->assertSee('LD-MILIKKU')
            ->assertSee('Admin L-DCH')
            ->assertSee('menyetujui penjual')
            ->assertDontSee('LD-ORANGLAIN')
            ->assertDontSee('Admin Rahasia');
    }
}
