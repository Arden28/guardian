<?php

namespace Arden28\Guardian\Services;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Arden28\Guardian\Events\UserLoggedIn;

class SocialAuthService
{
    /**
     * Handle social login for a given provider.
     *
     * @param string $provider
     * @param array|null $telegramData
     * @return array
     * @throws \Exception
     */
    public function handleSocialLogin($provider, $telegramData = null)
    {
        // Validate provider
        if (!array_key_exists($provider, config('guardian.socialite.drivers', []))) {
            throw new \Exception('Unsupported provider');
        }

        // Get user from social provider
        if ($provider === 'telegram' && $telegramData) {
            $socialUser = Socialite::driver('telegram')->validateTelegramData($telegramData);
        } else {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        }

        // Find or create user
        $userModel = config('guardian.user_model', 'App\Models\User');
        $user = $userModel::firstOrCreate(
            [
                'social_provider' => $provider,
                'social_id' => $socialUser->id,
            ],
            [
                'name' => $socialUser->name ?? $socialUser->email,
                'email' => $socialUser->email,
                'is_active' => true,
            ]
        );

        // Assign default role if new user
        if ($user->wasRecentlyCreated) {
            $user->assignRole(config('guardian.roles.default_role', 'user'));
        }

        // Log in the user
        Auth::login($user);

        // Issue Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Dispatch login event
        event(new UserLoggedIn($user));

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}