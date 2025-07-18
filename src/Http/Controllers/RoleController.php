<?php

namespace Arden28\Guardian\Http\Controllers;

use App\Http\Controllers\Controller; // Laravel default controller
use Arden28\Guardian\Http\Requests\AssignRoleRequest;
use Arden28\Guardian\Http\Requests\AssignPermissionRequest;
use Arden28\Guardian\Services\RoleService;

class RoleController extends Controller
{
    /**
     * The role service instance.
     *
     * @var RoleService
     */
    protected $roleService;

    /**
     * Create a new controller instance.
     *
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Assign a role to a user.
     *
     * @param AssignRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRole(AssignRoleRequest $request)
    {
        try {
            $userModel = config('guardian.user_model', 'App\Models\User');
            $user = $userModel::findOrFail($request->user_id);
            $this->roleService->assignRole($user, $request->role, $request->guard ?? 'api');

            return response()->json(['message' => 'Role assigned successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove a role from a user.
     *
     * @param AssignRoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeRole(AssignRoleRequest $request)
    {
        try {
            $userModel = config('guardian.user_model', 'App\Models\User');
            $user = $userModel::findOrFail($request->user_id);
            $this->roleService->removeRole($user, $request->role, $request->guard ?? 'api');

            return response()->json(['message' => 'Role removed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Assign a permission to a user.
     *
     * @param AssignPermissionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPermission(AssignPermissionRequest $request)
    {
        try {
            $userModel = config('guardian.user_model', 'App\Models\User');
            $user = $userModel::findOrFail($request->user_id);
            $this->roleService->assignPermission($user, $request->permission, $request->guard ?? 'api');

            return response()->json(['message' => 'Permission assigned successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Remove a permission from a user.
     *
     * @param AssignPermissionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removePermission(AssignPermissionRequest $request)
    {
        try {
            $userModel = config('guardian.user_model', 'App\Models\User');
            $user = $userModel::findOrFail($request->user_id);
            $this->roleService->removePermission($user, $request->permission, $request->guard ?? 'api');

            return response()->json(['message' => 'Permission removed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Check if a user has a role or permission.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function check(\Illuminate\Http\Request $request)
    {
        try {
            $user = $request->user();
            $role = $request->input('role');
            $permission = $request->input('permission');
            $guard = $request->input('guard', 'api');

            $result = [];

            if ($role) {
                $result['has_role'] = $this->roleService->hasRole($user, $role, $guard);
            }

            if ($permission) {
                $result['has_permission'] = $this->roleService->hasPermission($user, $permission, $guard);
            }

            return response()->json([
                'message' => 'Check completed',
                'result' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}