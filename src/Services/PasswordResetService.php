<?php

namespace Arden28\Guardian\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Arden28\Guardian\Notifications\PasswordResetNotification;

class PasswordResetService
{
    /**
     * Request a password reset and send a reset code.
     *
     * @param string $email
     * @return void
     * @throws \Exception
     */
    public function requestReset($email)
    {
        $userModel = config('guardian.user_model', 'App\Models\User');
        $user = $userModel::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('User not found');
        }

        // Generate a reset token
        $token = Str::random(60);
        Cache::put("password_reset_{$user->id}", $token, config('guardian.password_reset.token_expiry', 3600));

        // Send reset notification
        $user->notify(new PasswordResetNotification($token));
    }

    /**
     * Reset the user's password using a token.
     *
     * @param string $email
     * @param string $token
     * @param string $password
     * @return void
     * @throws \Exception
     */
    public function reset($email, $token, $password)
    {
        $userModel = config('guardian.user_model', 'App\Models\User');
        $user = $userModel::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('User not found');
        }

        $cachedToken = Cache::get("password_reset_{$user->id}");

        if (!$cachedToken || $cachedToken !== $token) {
            throw new \Exception('Invalid or expired reset token');
        }

        // Update password
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($password)]);

        // Clear token
        Cache::forget("password_reset_{$user->id}");
    }
}