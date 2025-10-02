<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Scopes\TenantsScope;
use Illuminate\Support\Facades\Auth;

class AttendanceSession extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'start_time',
        'end_time',
        'is_active',
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

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

    protected static function booted(): void
    {
        if (!app()->runningInConsole()) {
            static::addGlobalScope(new TenantsScope);

            static::creating(function ($model) {
                $model->tenant_id = $model->tenant_id ?? Auth::user()?->tenant_id;
            });
        }
    }
}
