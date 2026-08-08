<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function createTestUser(string $suffix = '1'): User
    {
        Hash::setRounds(4);
        return User::create([
            'name' => 'Verify User ' . $suffix,
            'username' => 'veruser' . $suffix,
            'email' => 'veruser' . $suffix . '@mahasiswa.unikom.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);
    }

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = $this->createTestUser('1');

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = $this->createTestUser('2');

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id_user, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect();
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = $this->createTestUser('3');

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id_user, 'hash' => sha1('wrong-email')]
        );

        $response = $this->actingAs($user)->get($verificationUrl);
        $response->assertStatus(403);
    }
}
