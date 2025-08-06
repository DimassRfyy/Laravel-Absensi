<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceSession extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    /**
     * Get the attendances for the session.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Check if current time is within this session's time range.
     */
    public function isActiveAt($time = null)
    {
        $time = $time ?? now();
        $currentTime = Carbon::parse($time)->format('H:i:s');
        
        return $currentTime >= $this->start_time && $currentTime <= $this->end_time;
    }

    /**
     * Get active session for current time.
     */
    public static function getActiveSession($time = null)
    {
        $time = $time ?? now();
        $currentTime = Carbon::parse($time)->format('H:i:s');
        
        return static::where('start_time', '<=', $currentTime)
                    ->where('end_time', '>=', $currentTime)
                    ->first();
    }
}
