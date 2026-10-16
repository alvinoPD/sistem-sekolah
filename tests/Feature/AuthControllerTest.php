<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_login_page_is_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('name="email"', false);
        $response->assertSee('action="'.route('login.authenticate').'"', false);
    }

    public function test_valid_credentials_authenticate_user_and_redirect_to_admin(): void
    {
        $user = User::create([
            'name' => 'Ayu Lestari',
            'email' => 'ayu@example.test',
            'password' => 'correct-password',
        ]);
        $user->role = 'admin';
        $user->save();

        $response = $this->post(route('login.authenticate'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertRedirect(route('admin.beranda'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_valid_user_credentials_redirect_to_user_beranda(): void
    {
        $user = User::create([
            'name' => 'Ayu Lestari',
            'email' => 'ayu@example.test',
            'password' => 'correct-password',
        ]);
        $user->role = 'user';
        $user->save();

        $response = $this->post(route('login.authenticate'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertRedirect(route('user.beranda'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_return_to_login_with_error(): void
    {
        $user = User::create([
            'name' => 'Ayu Lestari',
            'email' => 'ayu@example.test',
            'password' => 'correct-password',
        ]);

        $response = $this->from(route('login'))->post(route('login.authenticate'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login_from_admin(): void
    {
        $response = $this->get(route('admin.beranda'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_cannot_access_admin_beranda(): void
    {
        $user = User::create([
            'name' => 'Ayu Lestari',
            'email' => 'ayu@example.test',
            'password' => 'correct-password',
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get(route('admin.beranda'));

        $response->assertForbidden();
    }

    public function test_admin_is_redirected_to_admin_beranda_from_user_beranda(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'correct-password',
        ]);
        $admin->role = 'admin';
        $admin->save();

        $response = $this->actingAs($admin)->get(route('user.beranda'));

        $response->assertRedirect(route('admin.beranda'));
    }

    public function test_logout_ends_the_authenticated_session(): void
    {
        $user = User::create([
            'name' => 'Ayu Lestari',
            'email' => 'ayu@example.test',
            'password' => 'correct-password',
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
