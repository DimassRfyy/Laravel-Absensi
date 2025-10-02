<?php

namespace Database\Seeders;

use App\Models\AttendanceSession;
use App\Models\User;
use App\Models\Tenant;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Super admin global
        User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'super_admin',
        ]);

        $tenants = [
            [
                'name' => 'SMK Alpha',
                'email' => 'alpha@gmail.com',
                'phone' => '081234567890',
                'address' => 'Jl. Alpha No. 1',
                'is_active' => true,
                'plan' => 'free',
                'plan_expiry' => null,
            ],
            [
                'name' => 'SMK Beta',
                'email' => 'beta@gmail.com',
                'phone' => '089876543210',
                'address' => 'Jl. Beta No. 2',
                'is_active' => true,
                'plan' => 'free',
                'plan_expiry' => null,
            ],
        ];

        $classes = ['X', 'XI', 'XII'];
        $majors = ['IPA', 'IPS', 'Bahasa'];

        foreach ($tenants as $tIdx => $tenantData) {
            $tenant = Tenant::create($tenantData);

            // Admin untuk tenant
            User::create([
                'tenant_id' => $tenant->id,
                'name' => 'admin_' . strtolower($tenant->name),
                'email' => strtolower(str_replace(' ', '', $tenant->name)) . '@gmail.com',
                'password' => bcrypt('12345678'),
                'role' => 'admin',
            ]);

            // 7 student untuk tenant
            for ($i = 1; $i <= 7; $i++) {
                User::create([
                    'tenant_id' => $tenant->id,
                    'name' => 'Student ' . $i . ' ' . $tenant->name,
                    'email' => 'student' . $i . '_' . strtolower($tenant->name) . '@gmail.com',
                    'password' => bcrypt('12345678'),
                    'role' => 'student',
                    'class' => $classes[array_rand($classes)],
                    'major' => $majors[array_rand($majors)],
                    'parent_number' => '08' . str_pad((string)rand(100000000, 999999999), 9, '0', STR_PAD_LEFT),
                    'rfid' => 'RFID' . $tIdx . str_pad($i, 6, '0', STR_PAD_LEFT),
                ]);
            }

            // 2 attendance session untuk tenant
            $sessions = [
                [
                    'name' => 'Pagi',
                    'start_time' => '07:00:00',
                    'end_time' => '09:00:00',
                    'tenant_id' => $tenant->id,
                ],
                [
                    'name' => 'Sore',
                    'start_time' => '13:00:00',
                    'end_time' => '15:00:00',
                    'tenant_id' => $tenant->id,
                ],
            ];
            foreach ($sessions as $session) {
                AttendanceSession::create($session);
            }
        }
    }
}
