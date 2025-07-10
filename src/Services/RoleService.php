<?php

namespace Arden28\Guardian\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleService
{
    /**
     * Assign a role to a user.
     *
     * @param mixed $user
     * @param string $role
     * @param string $guard
     * @return void
     * @throws \Exception
     */
    public function assignRole($user, $role, $guard = 'api')
    {
        if (!in_array($guard, config('guardian.roles.guards', []))) {
            throw new \Exception('Invalid guard');
        }

        $roleExists = Role::where('name', $role)->where('guard_name', $guard)->exists();
        if (!$roleExists) {
            throw new \Exception("Role '{$role}' does not exist for guard '{$guard}'");
        }

        $user->assignRole($role, $guard);
    }

    /**
     * Remove a role from a user.
     *
     * @param mixed $user
     * @param string $role
     * @param string $guard
     * @return void
     * @throws \Exception
     */
    public function removeRole($user, $role, $guard = 'api')
    {
        if (!in_array($guard, config('guardian.roles.guards', []))) {
            throw new \Exception('Invalid guard');
        }

        $user->removeRole($role, $guard);
    }

    /**
     * Assign a permission to a user.
     *
     * @param mixed $user
     * @param string $permission
     * @param string $guard
     * @return void
     * @throws \Exception
     */
    public function assignPermission($user, $permission, $guard = 'api')
    {
        if (!in_array($guard, config('guardian.roles.guards', []))) {
            throw new \Exception('Invalid guard');
        }

        $permissionExists = Permission::where('name', $permission)->where('guard_name', $guard)->exists();
        if (!$permissionExists) {
            throw new \Exception("Permission '{$permission}' does not exist for guard '{$guard}'");
        }

        $user->givePermissionTo($permission, $guard);
    }

    /**
     * Remove a permission from a user.
     *
     * @param mixed $user
     * @param string $permission
     * @param string $guard
     * @return void
     * @throws \Exception
     */
    public function removePermission($user, $permission, $guard = 'api')
    {
        if (!in_array($guard, config('guardian.roles.guards', []))) {
            throw new \Exception('Invalid guard');
        }

        $user->revokePermissionTo($permission, $guard);
    }

    /**
     * Check if a user has a role.
     *
     * @param mixed $user
     * @param string $role
     * @param string $guard
     * @return bool
     */
    public function hasRole($user, $role, $guard = 'api')
    {
        return $user->hasRole($role, $guard);
    }

    /**
     * Check if a user has a permission.
     *
     * @param mixed $user
     * @param string $permission
     * @param string $guard
     * @return bool
     */
    public function hasPermission($user, $permission, $guard = 'api')
    {
        return $user->hasPermissionTo($permission, $guard);
    }

    /**
     * Create a new role.
     *
     * @param string $role
     * @param string $guard
     * @return Role
     */
    public function createRole($role, $guard = 'api')
    {
        if (!in_array($guard, config('guardian.roles.guards', []))) {
            throw new \Exception('Invalid guard');
        }

        return Role::create(['name' => $role, 'guard_name' => $guard]);
    }

    /**
     * Create a new permission.
     *
     * @param string $permission
     * @param string $guard
     * @return Permission
     */
    public function createPermission($permission, $guard = 'api')
    {
        if (!in_array($guard, config('guardian.roles.guards', []))) {
            throw new \Exception('Invalid guard');
        }

        return Permission::create(['name' => $permission, 'guard_name' => $guard]);
    }
}