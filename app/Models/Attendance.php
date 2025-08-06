<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'attendance_session_id',
        'status',
        'scanned_at',
    ];

    /**
     * Get the user that owns the attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendance session that owns the attendance.
     */
    public function attendanceSession()
    {
        return $this->belongsTo(AttendanceSession::class);
    }
}
