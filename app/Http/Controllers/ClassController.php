<?php

namespace App\Http\Controllers;

use App\Actions\CreateClass;
use App\Http\Requests\StoreClassRequest;
use App\Models\Student;
use App\Models\TrainingClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClassController extends Controller
{
    /**
     * List the signed-in coach's classes.
     */
    public function index(): View
    {
        $classes = TrainingClass::query()
            ->where('coach_id', Auth::id())
            ->withCount(['sessions', 'students'])
            ->latest()
            ->get();

        return view('classes.index', compact('classes'));
    }

    /**
     * Show the create-class form.
     */
    public function create(): View
    {
        return view('classes.create');
    }

    /**
     * Store a new class and generate its session(s).
     */
    public function store(StoreClassRequest $request, CreateClass $createClass): RedirectResponse
    {
        $class = $createClass->execute($request->validated(), Auth::id());

        return redirect()
            ->route('classes.show', $class)
            ->with('status', 'Class created with '.$class->sessions()->count().' session(s).');
    }

    /**
     * Show a class with its sessions and enrolled students.
     */
    public function show(TrainingClass $class): View
    {
        abort_unless($class->coach_id === Auth::id(), 403);

        $class->load([
            'sessions' => fn ($q) => $q->orderBy('session_date')->withCount('attendances'),
            'students',
        ]);

        // Students not yet enrolled, for the assign form.
        $available = Student::query()
            ->where('is_active', true)
            ->whereNotIn('id', $class->students->pluck('id'))
            ->orderBy('first_name')
            ->get();

        return view('classes.show', compact('class', 'available'));
    }
}
