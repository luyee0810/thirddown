<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\TrainingClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EnrollmentController extends Controller
{
    /**
     * Assign students to a class.
     */
    public function store(Request $request, TrainingClass $class): AnonymousResourceCollection
    {
        abort_unless($class->coach_id === $request->user()->id, 403);

        $data = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:students,id'],
        ]);

        $pivot = [];
        foreach ($data['student_ids'] as $id) {
            $pivot[$id] = ['enrolled_at' => now()->toDateString(), 'status' => 'active'];
        }
        $class->students()->syncWithoutDetaching($pivot);

        return StudentResource::collection($class->students()->get());
    }

    /**
     * Remove a student from a class.
     */
    public function destroy(Request $request, TrainingClass $class, Student $student): JsonResponse
    {
        abort_unless($class->coach_id === $request->user()->id, 403);

        $class->students()->detach($student->id);

        return response()->json(['message' => 'Student removed from class.']);
    }

    /**
     * Enrol one student into several of the coach's classes at once.
     */
    public function storeForStudent(Request $request, Student $student): AnonymousResourceCollection
    {
        $data = $request->validate([
            'class_ids' => ['required', 'array'],
            'class_ids.*' => ['integer', 'exists:classes,id'],
        ]);

        $classes = TrainingClass::whereIn('id', $data['class_ids'])
            ->where('coach_id', $request->user()->id)
            ->get();

        foreach ($classes as $class) {
            $class->students()->syncWithoutDetaching([
                $student->id => ['enrolled_at' => now()->toDateString(), 'status' => 'active'],
            ]);
        }

        // Return the student's current classes.
        return \App\Http\Resources\ClassResource::collection(
            $student->classes()->where('coach_id', $request->user()->id)->get()
        );
    }
}
