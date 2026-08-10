<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define all permissions
        $permissions = [
            // Church Management
            'approve_church',
            'reject_church',
            'view_all_churches',
            'suspend_church',

            // Activity Management
            'create_activity',
            'edit_activity',
            'delete_activity',
            'delete_any_activity',
            'view_activities',

            // Comment Management
            'create_comment',
            'delete_comment',
            'delete_any_comment',
            'approve_comment',
            'moderate_comments',

            // Social Program Management
            'create_program',
            'edit_program',
            'delete_program',
            'manage_registrations',
            'view_registrations',

            // Member Management
            'view_members',
            'manage_members',
            'export_members',

            // General
            'view_analytics',
            'view_dashboard',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // 1. SUPER ADMIN - Full access
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->syncPermissions($permissions);

        // 2. CHURCH ADMIN - Can manage own church
        $churchAdminRole = Role::firstOrCreate(['name' => 'church_admin']);
        $churchAdminPermissions = [
            'create_activity',
            'edit_activity',
            'delete_activity',
            'create_comment',
            'delete_comment',
            'delete_any_comment',
            'moderate_comments',
            'create_program',
            'edit_program',
            'delete_program',
            'manage_registrations',
            'view_registrations',
            'view_members',
            'manage_members',
            'export_members',
            'view_analytics',
            'view_dashboard',
        ];
        $churchAdminRole->syncPermissions($churchAdminPermissions);

        // 3. MEMBER - Can create content and register programs
        $memberRole = Role::firstOrCreate(['name' => 'member']);
        $memberPermissions = [
            'create_activity',
            'edit_activity',
            'delete_activity',
            'view_activities',
            'create_comment',
            'delete_comment',
            'view_dashboard',
        ];
        $memberRole->syncPermissions($memberPermissions);

        // 4. GUEST - No permissions (optional, just for clarity)
        Role::firstOrCreate(['name' => 'guest']);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
