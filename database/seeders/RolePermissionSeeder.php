<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $customPermissions = [
            'view_dashboard',
            'view_scan_rfid',
        ];

        $allPermissions = Permission::all();

        foreach ($customPermissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web']
            );
        }

        $customPermissions = [
        'view_dashboard',
        'view_scan_rfid',   
        ];

        // Buat Role
        $admin   = Role::firstOrCreate(['name' => 'Admin']);
        $teacher = Role::firstOrCreate(['name' => 'Teacher']);
        $student = Role::firstOrCreate(['name' => 'Student']);

        // Ambil semua permission dari Shield
        $allPermissions = Permission::all();

        // Assign permission ke Admin (semua akses)
        $admin->syncPermissions($allPermissions);

        // Assign permission ke Teacher (Dashboard, Scan RFID, Attendance Sessions)
        $teacher->syncPermissions([
            'view_dashboard',
            'view_scan_rfid',
            'view_any_attendance_session',
            'view_attendance_session',
        ]);

        // Assign permission ke Student (Dashboard, Scan RFID)
        $student->syncPermissions([
            'view_dashboard',
            'view_scan_rfid',
        ]);

        // === Buat User Default untuk Testing ===
        $adminUser = User::firstOrCreate(
            ['email' => 'ryuuadmin@gmail.com'],
            ['name' => 'ryuuadmin', 'password' => bcrypt('12345678')]
        );
        $adminUser->assignRole($admin);

        $teacherUser = User::firstOrCreate(
            ['email' => 'amarteacher@gmail.com'],
            ['name' => 'amarteacher', 'password' => bcrypt('12345678')]
        );
        $teacherUser->assignRole($teacher);

        $studentUser = User::firstOrCreate(
            ['email' => 'amarstudent@gmail.com'],
            ['name' => 'amarstudent', 'password' => bcrypt('password')]
        );
        $studentUser->assignRole($student);

        $this->command->info('Roles, permissions, and default users created successfully!');

        Permission::firstOrCreate(['name' => 'view_dashboard', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view_scan_rfid', 'guard_name' => 'web']);
    }
}
