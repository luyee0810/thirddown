<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\Student;
use App\Models\TrainingClass;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $coachId = Auth::id();

        // Next sessions to mark (today onward).
        $upcoming = ClassSession::query()
            ->whereHas('trainingClass', fn ($q) => $q->where('coach_id', $coachId))
            ->whereDate('session_date', '>=', now()->toDateString())
            ->with(['trainingClass' => fn ($q) => $q->withCount('students')])
            ->withCount('attendances')
            ->orderBy('session_date')
            ->limit(5)
            ->get();

        $stats = [
            'classes' => TrainingClass::where('coach_id', $coachId)->count(),
            'students' => Student::where('is_active', true)->count(),
            'sessions' => ClassSession::whereHas('trainingClass', fn ($q) => $q->where('coach_id', $coachId))->count(),
        ];

        return view('dashboard', compact('upcoming', 'stats'));
    }
}
