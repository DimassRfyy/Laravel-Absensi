<?php

namespace Database\Seeders;

use App\Models\AttendanceSession;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Role
        $admin   = Role::firstOrCreate(['name' => 'Admin']);
        $teacher = Role::firstOrCreate(['name' => 'Teacher']);
        $student = Role::firstOrCreate(['name' => 'Student']);

        // Ambil semua permission
        $allPermissions = Permission::all();

        // Admin → semua akses
        $admin->syncPermissions($allPermissions);

        // Teacher → dashboard, scan RFID, attendance sessions
        $teacher->syncPermissions([
        'view_dashboard',
        'view_scan-rfid',
        'view_any_attendance_session',
        'view_attendance_session',
        ]);
    
        // Student → hanya dashboard & scan RFID
        $student->syncPermissions([
        'view_dashboard',
        'view_scan-rfid',
        ]);

        // Create admin user
        User::create([
            'name' => 'ryuuadmin',
            'email' => 'ryuuadmin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
        ]);

        $sessions = [
            [
                'name' => 'Pagi',
                'start_time' => '07:00:00',
                'end_time' => '09:00:00',
            ],
            [
                'name' => 'Sore',
                'start_time' => '13:00:00',
                'end_time' => '15:00:00',
            ],
        ];

        $this->call(RolePermissionSeeder::class);

        foreach ($sessions as $session) {
            AttendanceSession::create($session);
        }
    }
}
