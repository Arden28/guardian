<?php

namespace Arden28\Guardian\Tests\Feature\PasswordReset;

use Arden28\Guardian\Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class PasswordResetTest extends TestCase
{
    public function test_password_reset_request_sends_notification()
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/password/reset', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password reset token sent']);

        Notification::assertSentTo($user, \Arden28\Guardian\Notifications\PasswordResetNotification::class);
        $this->assertNotNull(Cache::get("password_reset_{$user->id}"));
    }

    public function test_password_reset_confirmation_updates_password()
    {
        $user = User::factory()->create(['password' => Hash::make('old_password')]);
        $token = 'test-token';
        Cache::put("password_reset_{$user->id}", $token, 3600);

        $response = $this->postJson('/api/auth/password/reset/confirm', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password reset successful']);

        $user->refresh();
        $this->assertTrue(Hash::check('new_password', $user->password));
        $this->assertNull(Cache::get("password_reset_{$user->id}"));
    }

    public function test_invalid_token_returns_400()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/password/reset/confirm', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertStatus(400)
            ->assertJsonStructure(['error']);
    }
}