<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Student;
use App\Models\TrainingClass;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'coaches' => User::where('role', 'coach')->count(),
            'parents' => User::where('role', 'parent')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'students' => Student::count(),
            'classes' => TrainingClass::count(),
            'sessions' => ClassSession::count(),
        ];

        $recentUsers = User::latest()->limit(6)->get();

        $upcoming = ClassSession::query()
            ->whereDate('session_date', '>=', now()->toDateString())
            ->with('trainingClass.coach')
            ->orderBy('session_date')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'upcoming'));
    }
}
