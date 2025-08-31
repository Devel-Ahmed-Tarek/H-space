<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole          = Role::create(['name' => 'Admin']);
        $projectManagerRole = Role::create(['name' => 'Project Manager']);
        $developerRole      = Role::create(['name' => 'Developer']);

        // Create permissions
        $permissions = [
            // Project permissions
            'view-projects',
            'create-projects',
            'edit-projects',
            'delete-projects',
            'approve-projects',

            // Task permissions
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks',
            'update-task-status',

            // User permissions
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Stats permissions
            'view-stats',

            // Notification permissions
            'view-notifications',
            'manage-notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());

        $projectManagerRole->givePermissionTo([
            'view-projects',
            'create-projects',
            'edit-projects',
            'delete-projects',
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks',
            'view-users',
            'view-stats',
            'view-notifications',
        ]);

        $developerRole->givePermissionTo([
            'view-projects',
            'view-tasks',
            'update-task-status',
            'view-notifications',
        ]);
    }
}
