<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\TrainingClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Assign selected students to a class.
     */
    public function store(Request $request, TrainingClass $class): RedirectResponse
    {
        abort_unless($class->coach_id === Auth::id(), 403);

        $data = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ]);

        // attach without detaching existing; ignore already-enrolled students.
        $pivot = [];
        foreach ($data['student_ids'] as $id) {
            $pivot[$id] = ['enrolled_at' => now()->toDateString(), 'status' => 'active'];
        }
        $class->students()->syncWithoutDetaching($pivot);

        return back()->with('status', count($data['student_ids']).' student(s) enrolled.');
    }

    /**
     * Remove a student from a class.
     */
    public function destroy(TrainingClass $class, Student $student): RedirectResponse
    {
        abort_unless($class->coach_id === Auth::id(), 403);

        $class->students()->detach($student->id);

        return back()->with('status', "{$student->full_name} removed from class.");
    }

    /**
     * Enrol one student into several of the coach's classes at once.
     */
    public function storeForStudent(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'class_ids' => ['required', 'array'],
            'class_ids.*' => ['integer', 'exists:classes,id'],
        ]);

        // Restrict to the coach's own classes.
        $classes = TrainingClass::whereIn('id', $data['class_ids'])
            ->where('coach_id', Auth::id())
            ->get();

        foreach ($classes as $class) {
            $class->students()->syncWithoutDetaching([
                $student->id => ['enrolled_at' => now()->toDateString(), 'status' => 'active'],
            ]);
        }

        return back()->with('status', "{$student->full_name} added to {$classes->count()} class(es).");
    }

    /**
     * Remove a student from one class (from the student page).
     */
    public function destroyForStudent(Student $student, TrainingClass $class): RedirectResponse
    {
        abort_unless($class->coach_id === Auth::id(), 403);

        $class->students()->detach($student->id);

        return back()->with('status', "{$student->full_name} removed from {$class->name}.");
    }
}
