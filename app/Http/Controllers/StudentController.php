<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use App\Models\TrainingClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * List all students.
     */
    public function index(): View
    {
        $students = Student::query()
            ->withCount('classes')
            ->orderBy('first_name')
            ->get();

        return view('students.index', compact('students'));
    }

    /**
     * Show the create-student form.
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * Store a new student.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $student = Student::create($request->validated() + ['is_active' => true]);

        return redirect()
            ->route('students.index')
            ->with('status', "{$student->full_name} added.");
    }

    /**
     * Show a student with their classes and a multi-class enrol picker.
     */
    public function show(Student $student): View
    {
        $coachId = Auth::id();

        // Classes (owned by this coach) the student is already in.
        $enrolled = $student->classes()
            ->where('coach_id', $coachId)
            ->orderBy('name')
            ->get();

        // This coach's other classes, available to enrol into.
        $available = TrainingClass::query()
            ->where('coach_id', $coachId)
            ->whereNotIn('id', $enrolled->pluck('id'))
            ->withCount('sessions')
            ->orderBy('name')
            ->get();

        return view('students.show', compact('student', 'enrolled', 'available'));
    }
}
