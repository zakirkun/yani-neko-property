<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_rbac_roles_receive_the_expected_permissions(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $superAdmin = Role::findByName('super-admin');
        $admin = Role::findByName('admin');
        $agent = Role::findByName('agent');

        $this->assertCount(28, $superAdmin->permissions);
        $this->assertTrue($admin->hasPermissionTo('view roles'));
        $this->assertFalse($admin->hasPermissionTo('create roles'));
        $this->assertFalse($admin->hasPermissionTo('update roles'));
        $this->assertFalse($admin->hasPermissionTo('delete roles'));
        $this->assertEqualsCanonicalizing(
            ['view properties', 'create properties', 'update properties', 'delete properties', 'view leads', 'create leads', 'update leads', 'delete leads'],
            $agent->permissions->pluck('name')->all()
        );
    }

    public function test_only_staff_roles_can_access_the_filament_panel(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $panel = Filament::getPanel('admin');

        foreach (['super-admin', 'admin', 'agent'] as $role) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $this->assertTrue($user->canAccessPanel($panel), "The {$role} role should access the panel.");
        }

        $this->assertFalse(User::factory()->create()->canAccessPanel($panel));
    }
}
