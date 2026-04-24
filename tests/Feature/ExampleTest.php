<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Root guests should be redirected to the login page.
     */
    public function test_guest_is_redirected_to_login_from_root(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    /**
     * Guests can open the login screen.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Login dashboard');
    }

    /**
     * Valid credentials should authenticate the user.
     */
    public function test_user_can_login_and_access_dashboard(): void
    {
        $user = User::factory()->admin()->create([
            'email' => 'kasir@example.test',
            'password' => 'password',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);

        $dashboardResponse = $this->get(route('dashboard'));

        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee($user->name);
    }

    /**
     * Cashiers should be redirected to cashier screen after login.
     */
    public function test_cashier_is_redirected_to_cashier_screen_after_login(): void
    {
        $user = User::factory()->cashier()->create([
            'email' => 'cashier@example.test',
            'password' => 'password',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('transactions.create', absolute: false));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Cashier login should ignore an intended admin URL and go to cashier screen.
     */
    public function test_cashier_login_ignores_intended_admin_url(): void
    {
        $user = User::factory()->cashier()->create([
            'email' => 'cashier-intended@example.test',
            'password' => 'password',
        ]);

        $this->get(route('dashboard'));

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('transactions.create', absolute: false));
        $this->assertAuthenticatedAs($user);
    }
}
