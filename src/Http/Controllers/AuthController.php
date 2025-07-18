<?php

namespace Arden28\Guardian\Http\Controllers;

use App\Http\Controllers\Controller; // Laravel default controller
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Arden28\Guardian\Http\Requests\LoginRequest;
use Arden28\Guardian\Http\Requests\RegisterRequest;
use Arden28\Guardian\Events\UserLoggedIn;
use Arden28\Guardian\Services\PasswordResetService;
use Arden28\Guardian\Services\TwoFactorService;

class AuthController extends Controller
{
    /**
     * The 2FA service instance.
     *
     * @var TwoFactorService
     */
    protected $twoFactorService;
    protected $passwordResetService;

    /**
     * Create a new controller instance.
     *
     * @param TwoFactorService $twoFactorService
     * @param PasswordResetService $passwordResetService
     */
    public function __construct(TwoFactorService $twoFactorService, PasswordResetService $passwordResetService)
    {
        $this->twoFactorService = $twoFactorService;
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Handle user login and issue a Sanctum token.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // Attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Check if 2FA is enabled
            if ($user->hasTwoFactorEnabled()) {
                // Send 2FA code
                try {
                    $this->twoFactorService->sendCode($user, $user->twoFactorSettings->method);
                    return response()->json([
                        'message' => '2FA verification required',
                        'requires_2fa' => true,
                        'user_id' => $user->id,
                    ], 200);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to send 2FA code: ' . $e->getMessage()], 400);
                }
            }

            // Issue Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Dispatch login event
            event(new UserLoggedIn($user));

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ], 200);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $userModel = config('guardian.user_model', 'App\Models\User');
        $user = $userModel::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Assign default role
        $user->assignRole(config('guardian.roles.default_role', 'user'));

        // Issue Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Log out the authenticated user and revoke their token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    /**
     * Get the authenticated user's details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ], 200);
    }

    /**
     * Request a password reset link.
     *
     * @param PasswordResetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestPasswordReset(PasswordResetRequest $request)
    {
        try {
            $this->passwordResetService->requestReset($request->email);
            return response()->json(['message' => 'Password reset token sent'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Reset the user's password.
     *
     * @param PasswordResetConfirmRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(PasswordResetConfirmRequest $request)
    {
        try {
            $this->passwordResetService->reset($request->email, $request->token, $request->password);
            return response()->json(['message' => 'Password reset successful'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}