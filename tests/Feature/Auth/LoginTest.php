<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_redirected_to_admin_dashboard_after_login(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post(route('login'), ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertNotNull($admin->fresh()->last_login_at);
    }

    public function test_seller_is_redirected_to_seller_dashboard_after_login(): void
    {
        $seller = User::factory()->seller()->create();

        $this->post(route('login'), ['email' => $seller->email, 'password' => 'password'])
            ->assertRedirect(route('seller.dashboard'));
    }

    public function test_wrong_password_is_rejected(): void
    {
        $seller = User::factory()->seller()->create();

        $this->post(route('login'), ['email' => $seller->email, 'password' => 'salah'])
            ->assertSessionHasErrors(['email' => 'Email atau kata sandi tidak sesuai.']);

        $this->assertGuest();
    }

    public function test_deactivated_account_cannot_log_in(): void
    {
        $seller = User::factory()->inactive()->seller()->create();

        $this->post(route('login'), ['email' => $seller->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_deactivated_account_is_logged_out_on_next_request(): void
    {
        $seller = User::factory()->inactive()->seller()->create();

        $this->actingAs($seller)
            ->get(route('seller.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        $seller = User::factory()->seller()->create();

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login'), ['email' => $seller->email, 'password' => 'salah']);
        }

        $this->post(route('login'), ['email' => $seller->email, 'password' => 'password'])
            ->assertTooManyRequests();
    }

    public function test_guest_is_redirected_to_login_from_dashboards(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('seller.dashboard'))->assertRedirect(route('login'));
    }

    public function test_seller_cannot_open_admin_panel_and_admin_cannot_open_seller_panel(): void
    {
        $this->actingAs(User::factory()->seller()->create())
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('seller.dashboard'))
            ->assertForbidden();
    }
}
