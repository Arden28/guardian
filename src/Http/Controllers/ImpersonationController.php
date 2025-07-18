<?php

namespace Arden28\Guardian\Http\Controllers;

use App\Http\Controllers\Controller; // Laravel default controller
use Illuminate\Support\Facades\Auth;
use Arden28\Guardian\Http\Requests\ImpersonationRequest;
use Arden28\Guardian\Models\ImpersonationLog;
use Illuminate\Support\Str;

class ImpersonationController extends Controller
{
    /**
     * Start an impersonation session.
     *
     * @param ImpersonationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function start(ImpersonationRequest $request)
    {
        $user = Auth::user();
        $userModel = config('guardian.user_model', 'App\Models\User');
        $targetUser = $userModel::findOrFail($request->user_id);

        // Check if the user can impersonate
        if (!$user->canImpersonate()) {
            return response()->json(['error' => 'Unauthorized to impersonate'], 403);
        }

        // Log the impersonation session
        $sessionId = Str::uuid()->toString();
        ImpersonationLog::create([
            'impersonator_id' => $user->id,
            'impersonated_id' => $targetUser->id,
            'session_id' => $sessionId,
            'started_at' => now(),
        ]);

        // Log in as the target user
        Auth::login($targetUser);

        // Issue a new token for the impersonated user
        $token = $targetUser->createToken('impersonation_token')->plainTextToken;

        return response()->json([
            'message' => 'Impersonation started',
            'user' => $targetUser,
            'token' => $token,
            'session_id' => $sessionId,
        ], 200);
    }

    /**
     * Stop the current impersonation session.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stop(Request $request)
    {
        // TODO: Implement stop impersonation logic
        return response()->json(['message' => 'Impersonation stopped'], 200);
    }
}