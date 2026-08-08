<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    private function createTestUser(string $suffix = '1'): User
    {
        Hash::setRounds(4);
        return User::create([
            'name' => 'Confirm User ' . $suffix,
            'username' => 'confuser' . $suffix,
            'email' => 'confuser' . $suffix . '@mahasiswa.unikom.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);
    }

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = $this->createTestUser('1');

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = $this->createTestUser('2');

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = $this->createTestUser('3');

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
