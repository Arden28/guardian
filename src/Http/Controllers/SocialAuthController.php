<?php

namespace Arden28\Guardian\Http\Controllers;

use Arden28\Guardian\Services\SocialAuthService;
use Arden28\Guardian\Http\Requests\SocialAuthRequest;

class SocialAuthController extends Controller
{
    /**
     * The social auth service instance.
     *
     * @var SocialAuthService
     */
    protected $socialAuthService;

    /**
     * Create a new controller instance.
     *
     * @param SocialAuthService $socialAuthService
     */
    public function __construct(SocialAuthService $socialAuthService)
    {
        $this->socialAuthService = $socialAuthService;
    }

    /**
     * Redirect the user to the social provider's authentication page.
     *
     * @param string $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirect($provider)
    {
        // Validate provider
        if (!array_key_exists($provider, config('guardian.socialite.drivers', []))) {
            return response()->json(['error' => 'Unsupported provider'], 400);
        }

        // For Telegram, return the redirect URI (handled by frontend)
        if ($provider === 'telegram') {
            return response()->json([
                'redirect_url' => config('guardian.socialite.drivers.telegram.redirect'),
            ], 200);
        }

        // Redirect to provider's auth page
        return response()->json([
            'redirect_url' => \Laravel\Socialite\Facades\Socialite::driver($provider)->stateless()->redirect()->getTargetUrl(),
        ], 200);
    }

    /**
     * Handle the callback from the social provider.
     *
     * @param SocialAuthRequest $request
     * @param string $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback(SocialAuthRequest $request, $provider)
    {
        try {
            // Handle Telegram separately
            $telegramData = $provider === 'telegram' ? $request->all() : null;

            // Process social login
            $result = $this->socialAuthService->handleSocialLogin($provider, $telegramData);

            return response()->json([
                'message' => 'Social login successful',
                'user' => $result['user'],
                'token' => $result['token'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Social login failed: ' . $e->getMessage()], 400);
        }
    }
}