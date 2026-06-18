<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\HolidaysController;
use App\Http\Controllers\ReportController;

Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeesController::class, 'index']);
    Route::post('/', [EmployeesController::class, 'store']);
    Route::get('/{id}', [EmployeesController::class, 'show']);
    Route::put('/{id}', [EmployeesController::class, 'update']);
    Route::delete('/{id}', [EmployeesController::class, 'destroy']);
});

Route::prefix('holidays')->group(function () {
    Route::get('/', [HolidaysController::class, 'index']);
    Route::post('/', [HolidaysController::class, 'store']);
    Route::get('/{id}', [HolidaysController::class, 'show']);
    Route::put('/{id}', [HolidaysController::class, 'update']);
    Route::delete('/{id}', [HolidaysController::class, 'destroy']);
});

Route::prefix('attendance')->group(function () {
    Route::post('/check-in', [AttendancesController::class, 'checkIn']);
    Route::post('/check-out', [AttendancesController::class, 'checkOut']);
    Route::post('/absence', [AttendancesController::class, 'absence']);
});

Route::get('/reports/monthly', [ReportController::class, 'monthlyReport']);
