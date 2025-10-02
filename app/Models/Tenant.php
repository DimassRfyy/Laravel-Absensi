<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'is_active',
        'plan',
        'plan_expiry',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
