<?php

namespace Arden28\Guardian\Http\Controllers;

use App\Http\Controllers\Controller; // Laravel default controller
use Arden28\Guardian\Http\Requests\TwoFactorRequest;
use Arden28\Guardian\Services\TwoFactorService;

class TwoFactorController extends Controller
{
    /**
     * The 2FA service instance.
     *
     * @var TwoFactorService
     */
    protected $twoFactorService;

    /**
     * Create a new controller instance.
     *
     * @param TwoFactorService $twoFactorService
     */
    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Send a 2FA code to the user.
     *
     * @param TwoFactorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(TwoFactorRequest $request)
    {
        $userModel = config('guardian.user_model', 'App\Models\User');
        $user = $userModel::where('email', $request->email)->firstOrFail();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json(['error' => '2FA not enabled'], 400);
        }

        try {
            $this->twoFactorService->sendCode($user, $user->twoFactorSettings->method);
            return response()->json(['message' => '2FA code sent'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send 2FA code: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Enable 2FA for the authenticated user.
     *
     * @param TwoFactorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enable(TwoFactorRequest $request)
    {
        try {
            $user = auth()->user();
            $data = $this->twoFactorService->enable($user, $request->method, $request->phone_number);

            return response()->json([
                'message' => '2FA enabled successfully',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to enable 2FA: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Verify 2FA code for the authenticated user.
     *
     * @param TwoFactorRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(TwoFactorRequest $request)
    {
        $user = auth()->user();

        if (!$user->hasTwoFactorEnabled()) {
            return response()->json(['error' => '2FA not enabled'], 400);
        }

        if ($this->twoFactorService->verifyCode($user, $user->twoFactorSettings->method, $request->code)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => '2FA verified successfully',
                'token' => $token,
            ], 200);
        }

        return response()->json(['error' => 'Invalid 2FA code'], 400);
    }

    /**
     * Disable 2FA for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disable(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $this->twoFactorService->disable($user);
        return response()->json(['message' => '2FA disabled successfully'], 200);
    }
}