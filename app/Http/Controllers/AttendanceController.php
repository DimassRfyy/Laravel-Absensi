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

        // Kirim WhatsApp ke parent_number jika ada
        if (!empty($student->parent_number)) {
            $this->sendWhatsappNotification($student, $activeSession, $scannedAt);
        }

        return redirect()->back()->with('success', "Absensi berhasil dicatat: {$student->name} - {$activeSession->name} pada " . $scannedAt->format('H:i:s'));
    }

    /**
     * Kirim notifikasi WhatsApp ke parent_number menggunakan Fonnte API
     */
    private function sendWhatsappNotification($student, $activeSession, $scannedAt)
    {
        $token = '7ET3uCqV6CZMqbFG88r8';
        $target = $student->parent_number;
        $message = "Halo Orang Tua/Wali dari {$student->name},\n\nKami ingin menginformasikan bahwa putra/putri Anda telah melakukan absensi pada sesi {$activeSession->name} di waktu " . $scannedAt->format('H:i:s') . ".\n\nTerima kasih atas dukungan Anda dalam memantau kehadiran siswa. Jika ada pertanyaan, silakan hubungi pihak sekolah.\n\nSalam hormat,\nAdmin Absensi";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        // Log response jika perlu
        // 
        // 
        // 
    }
}