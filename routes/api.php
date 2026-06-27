<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Authenticated (Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user'])->name('api.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Classes
    Route::get('/classes', [ClassController::class, 'index'])->name('api.classes.index');
    Route::post('/classes', [ClassController::class, 'store'])->name('api.classes.store');
    Route::get('/classes/{class}', [ClassController::class, 'show'])->name('api.classes.show');

    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('api.students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('api.students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('api.students.show');

    // Enrollment
    Route::post('/classes/{class}/students', [EnrollmentController::class, 'store'])->name('api.classes.students.store');
    Route::delete('/classes/{class}/students/{student}', [EnrollmentController::class, 'destroy'])->name('api.classes.students.destroy');
    Route::post('/students/{student}/classes', [EnrollmentController::class, 'storeForStudent'])->name('api.students.classes.store');

    // Sessions
    Route::get('/sessions', [SessionController::class, 'index'])->name('api.sessions.index');

    // Attendance
    Route::get('/sessions/{session}/attendance', [AttendanceController::class, 'index'])->name('api.attendance.index');
    Route::post('/sessions/{session}/attendance', [AttendanceController::class, 'store'])->name('api.attendance.store');
});
