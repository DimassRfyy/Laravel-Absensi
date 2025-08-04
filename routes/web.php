<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/attendance', [\App\Http\Controllers\AttendanceController::class, 'store'])
    ->name('attendance.store');
