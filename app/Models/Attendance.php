<?php

namespace App\Models;

use App\Models\Scopes\TenantsScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Attendance extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'attendance_session_id',
        'status',
        'scanned_at',
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

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
