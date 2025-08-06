<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'rfid' => 'required|string',
        ]);

        $student = User::where('rfid', $request->input('rfid'))->first();

        if (!$student) {
            return redirect()->back()->with('error', 'RFID tidak ditemukan.');
        }

        // Set waktu scan saat ini
        $scannedAt = now();
        
        // Cari sesi aktif berdasarkan waktu scan
        $activeSession = AttendanceSession::getActiveSession($scannedAt);
        
        if (!$activeSession) {
            return redirect()->back()->with('error', 'Tidak ada sesi absensi yang aktif pada waktu ini.');
        }

        // Cek apakah user sudah absen pada sesi ini hari ini
        $existingAttendance = Attendance::where('user_id', $student->id)
            ->where('attendance_session_id', $activeSession->id)
            ->whereDate('scanned_at', $scannedAt->toDateString())
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', "Anda sudah absen pada sesi {$activeSession->name} hari ini.");
        }

        // Simpan data absensi dengan sesi yang sesuai
        Attendance::create([
            'user_id' => $student->id,
            'attendance_session_id' => $activeSession->id,
            'status' => 'hadir',
            'scanned_at' => $scannedAt,
        ]);

        return redirect()->back()->with('success', "Absensi berhasil dicatat: {$student->name} - {$activeSession->name} pada " . $scannedAt->format('H:i:s'));
    }
}