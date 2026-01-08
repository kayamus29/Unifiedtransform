<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // 1. Reset Cached Roles/Permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Define Permissions (Strict List)
        $permissions = [
            // Accountant
            'view accounting dashboard',
            'manage fee heads',
            'assign fees',
            'collect fees',
            'manage expenses',
            // Teacher
            'view assigned classes',
            'take attendance',
            'manage marks',
            'view assigned syllabus',
            // Staff
            'staff check-in',
            'create expense transfer', // Shared by Teacher & Staff
            'create expenses', // New permission
            // Student/Parent
            'view own attendance',
            'view own marks',
            'view child records',
            // Admin Only (Explicitly defined for clarity)
            'assign teachers',
            'edit classes',
            'edit sections',
            'edit courses',
            'view classes',
            'view audit logs',
            'view assigned students',
            'view users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Create Roles & Assign Permissions

        // Admin: No permissions assigned (handled by Gate::before)
        Role::firstOrCreate(['name' => 'Admin']);

        // Accountant
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        $accountant->syncPermissions([
            'view accounting dashboard',
            'manage fee heads',
            'assign fees',
            'collect fees',
            'manage expenses',
            'staff check-in', // Logic: Accountants usually check in too
            'create expenses', // Assigned to Accountant
        ]);

        // Teacher
        $teacher = Role::firstOrCreate(['name' => 'Teacher']);
        $teacher->syncPermissions([
            'view assigned classes',
            'take attendance',
            'manage marks',
            'view assigned syllabus',
            'staff check-in',
            'create expense transfer',
            'view assigned students',
        ]);

        // Normal Staff (covers existing 'librarian' & 'staff')
        $staff = Role::firstOrCreate(['name' => 'Normal Staff']);
        $staff->syncPermissions([
            'staff check-in',
            'create expense transfer',
        ]);

        // Student
        $student = Role::firstOrCreate(['name' => 'Student']);
        $student->syncPermissions([
            'view own attendance',
            'view own marks',
        ]);

        // Parent
        $parent = Role::firstOrCreate(['name' => 'Parent']);
        $parent->syncPermissions([
            'view child records',
        ]);

        // 4. Migration Logic: Sync Legacy Roles to Spatie Roles
        $users = User::all();
        foreach ($users as $user) {
            // Map legacy role string to Spatie Role
            $roleToAssign = null;
            switch (strtolower($user->role)) {
                case 'admin':
                    $roleToAssign = 'Admin';
                    break;
                case 'teacher':
                    $roleToAssign = 'Teacher';
                    break;
                case 'accountant':
                    $roleToAssign = 'Accountant';
                    break;
                case 'student':
                    $roleToAssign = 'Student';
                    break;
                case 'parent':
                    $roleToAssign = 'Parent';
                    break;
                case 'librarian':
                case 'staff':
                    $roleToAssign = 'Normal Staff';
                    break;
            }

            if ($roleToAssign) {
                // Sync roles to ensure clean state (removes previous roles if re-run)
                $user->syncRoles($roleToAssign);
            }
        }
    }
}
