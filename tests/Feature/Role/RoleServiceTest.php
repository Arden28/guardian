<?php

namespace Arden28\Guardian\Tests\Unit\Services;

use Arden28\Guardian\Tests\TestCase;
use Arden28\Guardian\Services\RoleService;
use Arden28\Guardian\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleServiceTest extends TestCase
{
    protected $roleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleService = new RoleService();
    }

    public function test_assign_role()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'test-role', 'guard_name' => 'api']);

        $this->roleService->assignRole($user, 'test-role', 'api');

        $this->assertTrue($user->hasRole('test-role', 'api'));
    }

    public function test_assign_permission()
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'test-permission', 'guard_name' => 'api']);

        $this->roleService->assignPermission($user, 'test-permission', 'api');

        $this->assertTrue($user->hasPermissionTo('test-permission', 'api'));
    }

    public function test_invalid_guard_throws_exception()
    {
        $user = User::factory()->create();
        Role::create(['name' => 'test-role', 'guard_name' => 'api']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid guard');

        $this->roleService->assignRole($user, 'test-role', 'invalid');
    }
}