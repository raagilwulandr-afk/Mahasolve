<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        Hash::setRounds(4);
        $user = User::create([
            'name' => 'Auth Test User',
            'username' => 'authtest',
            'email' => 'authtest@mahasiswa.unikom.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        Hash::setRounds(4);
        $user = User::create([
            'name' => 'Auth Test User 2',
            'username' => 'authtest2',
            'email' => 'authtest2@mahasiswa.unikom.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        Hash::setRounds(4);
        $user = User::create([
            'name' => 'Auth Test User 3',
            'username' => 'authtest3',
            'email' => 'authtest3@mahasiswa.unikom.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
