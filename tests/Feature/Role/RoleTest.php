<?php

namespace Arden28\Guardian\Tests\Feature\Role;

use Arden28\Guardian\Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTest extends TestCase
{
    public function test_admin_can_assign_role()
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();
        Role::create(['name' => 'admin', 'guard_name' => 'api']);
        Permission::create(['name' => 'manage_roles', 'guard_name' => 'api']);
        $admin->assignRole('admin');
        $admin->givePermissionTo('manage_roles');
        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/auth/roles/assign', [
                'user_id' => $user->id,
                'role' => 'admin',
                'guard' => 'api',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Role assigned successfully']);
        
        $this->assertTrue($user->hasRole('admin', 'api'));
    }

    public function test_non_admin_cannot_assign_role()
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        Role::create(['name' => 'admin', 'guard_name' => 'api']);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/auth/roles/assign', [
                'user_id' => $targetUser->id,
                'role' => 'admin',
                'guard' => 'api',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_check_role()
    {
        $user = User::factory()->create();
        $user->assignRole('user', 'api');
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/auth/check', [
                'role' => 'user',
                'guard' => 'api',
            ]);

        $response->assertStatus(200)
            ->assertJson(['result' => ['has_role' => true]]);
    }
}