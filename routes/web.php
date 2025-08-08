<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::get('/scan-rfid', function () {
    return view('welcome');
});

Route::post('/attendance', [\App\Http\Controllers\AttendanceController::class, 'store'])
    ->name('attendance.store');
