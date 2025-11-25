<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\Attendance;
use App\Models\User;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/clock-in', [\App\Http\Controllers\AttendanceController::class, 'clockIn']);
    Route::post('/clock-out', [\App\Http\Controllers\AttendanceController::class, 'clockOut']);
    Route::get('/history', [\App\Http\Controllers\AttendanceController::class, 'history']);
});

Route::get('/debug/users', function () {
    return User::all();
});

Route::get('/debug/attendances', function () {
    return Attendance::all();
});

Route::get('/debug/clear-attendances', function () {
    Attendance::query()->delete();
    return 'All attendances cleared.';
});
