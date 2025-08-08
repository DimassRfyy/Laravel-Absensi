<?php

namespace Database\Seeders;

use App\Models\AttendanceSession;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

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

        foreach ($sessions as $session) {
            AttendanceSession::create($session);
        }
    }
}
