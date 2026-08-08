<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createTestUser(string $suffix = '1'): User
    {
        Hash::setRounds(4);
        return User::create([
            'name' => 'Profile User ' . $suffix,
            'username' => 'profuser' . $suffix,
            'email' => 'profuser' . $suffix . '@mahasiswa.unikom.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);
    }

    public function test_profile_page_is_displayed(): void
    {
        $user = $this->createTestUser('1');

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->createTestUser('2');

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Updated User',
                'username' => 'updated_username',
                'email' => 'updated@mahasiswa.unikom.ac.id',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('updated_username', $user->username);
        $this->assertSame('updated@mahasiswa.unikom.ac.id', $user->email);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = $this->createTestUser('3');

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'username' => $user->username,
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = $this->createTestUser('4');

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->createTestUser('5');

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
