<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SessionController extends Controller
{
    /**
     * List the coach's sessions, split into upcoming and past, with attendance status.
     */
    public function index(): View
    {
        $sessions = ClassSession::query()
            ->whereHas('trainingClass', fn ($q) => $q->where('coach_id', Auth::id()))
            ->with(['trainingClass' => fn ($q) => $q->withCount('students')])
            ->withCount('attendances')
            ->orderBy('session_date')
            ->get();

        $today = now()->startOfDay();

        $upcoming = $sessions->filter(fn ($s) => $s->session_date->gte($today))->values();
        $past = $sessions->filter(fn ($s) => $s->session_date->lt($today))->sortByDesc('session_date')->values();

        return view('sessions.index', compact('upcoming', 'past'));
    }
}
