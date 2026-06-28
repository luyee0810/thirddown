<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\Parent\ChildController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(Auth::check() ? Auth::user()->homeRoute() : 'login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

// Parent area
Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/', [ChildController::class, 'index'])->name('dashboard');
    Route::get('/children/create', [ChildController::class, 'create'])->name('children.create');
    Route::post('/children', [ChildController::class, 'store'])->name('children.store');
    Route::get('/children/{child}/edit', [ChildController::class, 'edit'])->name('children.edit');
    Route::put('/children/{child}', [ChildController::class, 'update'])->name('children.update');
});

// Coach / admin area
Route::middleware(['auth', 'role:coach,admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Classes
    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}', [ClassController::class, 'show'])->name('classes.show');

    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');

    // Enrol one student into many classes (from the student page)
    Route::post('/students/{student}/classes', [EnrollmentController::class, 'storeForStudent'])->name('students.classes.store');
    Route::delete('/students/{student}/classes/{class}', [EnrollmentController::class, 'destroyForStudent'])->name('students.classes.destroy');

    // Enrollment (assign students to a class)
    Route::post('/classes/{class}/students', [EnrollmentController::class, 'store'])->name('classes.students.store');
    Route::delete('/classes/{class}/students/{student}', [EnrollmentController::class, 'destroy'])->name('classes.students.destroy');

    // Sessions
    Route::get('/sessions', [SessionController::class, 'index'])->name('sessions.index');

    // Attendance marking
    Route::get('/sessions/{session}/attendance', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::post('/sessions/{session}/attendance', [AttendanceController::class, 'update'])->name('attendance.update');
});
