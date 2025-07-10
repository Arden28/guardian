<?php

namespace Arden28\Guardian\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Arden28\Guardian\Events\UserLoggedIn;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the social provider's authentication page.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect($provider)
    {
        // Validate provider against configured socialite drivers
        if (!array_key_exists($provider, config('guardian.socialite.drivers', []))) {
            return response()->json(['error' => 'Unsupported provider'], 400);
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Handle the callback from the social provider and authenticate the user.
     *
     * @param string $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback($provider)
    {
        try {
            // Get user from social provider
            $socialUser = Socialite::driver($provider)->stateless()->user();

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

            return response()->json([
                'message' => 'Social login successful',
                'user' => $user,
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Social login failed: ' . $e->getMessage()], 400);
        }
    }
}