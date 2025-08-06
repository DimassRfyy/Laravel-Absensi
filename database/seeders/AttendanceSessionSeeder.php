<?php

namespace Database\Seeders;

use App\Models\AttendanceSession;
use Illuminate\Database\Seeder;

class AttendanceSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            [
                'name' => 'Matematika',
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
            ],
            [
                'name' => 'Bahasa Indonesia',
                'start_time' => '09:30:00',
                'end_time' => '11:00:00',
            ],
            [
                'name' => 'IPA',
                'start_time' => '11:00:00',
                'end_time' => '12:30:00',
            ],
        ];

        foreach ($sessions as $session) {
            AttendanceSession::create($session);
        }
    }
}
