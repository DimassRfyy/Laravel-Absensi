<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $student = User::where('rfid', $request->input('rfid'))->first();

        if (!$student) {
            return redirect()->back()->with('error', 'RFID tidak ditemukan.');
        }

        $request->validate([
            'rfid' => 'required|string',
        ]);

        // Set waktu scan saat ini
        $scannedAt = now();
        
        

        // Simpan data absensi dengan status 'hadir'
        Attendance::create([
            'user_id' => $student->id,
            'status' => 'hadir',
            'scanned_at' => $scannedAt,
        ]);

        return redirect()->back()->with('success', "Absensi berhasil dicatat: {$student->name} - hadir pada " . $scannedAt->format('H:i:s'));
    }
}