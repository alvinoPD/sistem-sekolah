<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_registration_page_is_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('name="name"', false);
        $response->assertSee('name="password_confirmation"', false);
        $response->assertSee('action="'.route('register.store').'"', false);
    }

    public function test_valid_registration_creates_and_authenticates_user(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Ayu Lestari',
            'email' => 'ayu@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $response->assertRedirect(route('user.beranda'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Ayu Lestari',
            'email' => 'ayu@example.test',
        ]);
        $this->assertTrue(Hash::check('secure-password', User::where('email', 'ayu@example.test')->firstOrFail()->password));
    }

    public function test_duplicate_email_is_rejected_without_creating_another_user(): void
    {
        $user = User::create([
            'name' => 'Ayu Lestari',
            'email' => 'ayu@example.test',
            'password' => 'correct-password',
        ]);

        $response = $this->from('/register')->post(route('register.store'), [
            'name' => 'Another User',
            'email' => $user->email,
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    public function test_mismatched_password_confirmation_is_rejected(): void
    {
        $response = $this->from('/register')->post(route('register.store'), [
            'name' => 'Ayu Lestari',
            'email' => 'ayu@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }
}
