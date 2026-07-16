<?php

use App\Http\Controllers\Admin\ClassController as AdminClassController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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

// Admin area — site-wide management.
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users (coaches, parents, admins)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{user}/reassign-classes', [AdminUserController::class, 'reassignClasses'])->name('users.reassign-classes');

    // Students
    Route::get('/students', [AdminStudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [AdminStudentController::class, 'create'])->name('students.create');
    Route::post('/students', [AdminStudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [AdminStudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [AdminStudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [AdminStudentController::class, 'destroy'])->name('students.destroy');

    // Classes
    Route::get('/classes', [AdminClassController::class, 'index'])->name('classes.index');
    Route::put('/classes/{class}/coach', [AdminClassController::class, 'updateCoach'])->name('classes.coach');
    Route::delete('/classes/{class}', [AdminClassController::class, 'destroy'])->name('classes.destroy');
});

// Coach area
Route::middleware(['auth', 'role:coach'])->group(function () {
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
