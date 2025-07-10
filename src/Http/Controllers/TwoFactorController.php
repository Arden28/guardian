<?php

namespace Arden28\Guardian\Http\Controllers;

use Arden28\Guardian\Http\Requests\TwoFactorRequest;

class TwoFactorController extends Controller
{
    /**
     * Enable 2FA for the authenticated user.
     *
     * @param TwoFactorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enable(TwoFactorRequest $request)
    {
        // TODO: Implement 2FA enable logic (email, SMS, TOTP)
        return response()->json(['message' => '2FA enabled successfully'], 200);
    }

    /**
     * Verify 2FA code for the authenticated user.
     *
     * @param TwoFactorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(TwoFactorRequest $request)
    {
        // TODO: Implement 2FA verification logic
        return response()->json(['message' => '2FA verified successfully'], 200);
    }

    /**
     * Disable 2FA for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disable(Request $request)
    {
        // TODO: Implement 2FA disable logic
        return response()->json(['message' => '2FA disabled successfully'], 200);
    }
}