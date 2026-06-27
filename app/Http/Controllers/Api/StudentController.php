<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StudentController extends Controller
{
    /**
     * List all students.
     */
    public function index(): AnonymousResourceCollection
    {
        $students = Student::query()
            ->withCount('classes')
            ->orderBy('first_name')
            ->get();

        return StudentResource::collection($students);
    }

    /**
     * Create a student.
     */
    public function store(StoreStudentRequest $request): StudentResource
    {
        $student = Student::create($request->validated() + ['is_active' => true]);

        return (new StudentResource($student))
            ->additional(['message' => 'Student created.']);
    }

    /**
     * Show a student.
     */
    public function show(Student $student): StudentResource
    {
        return new StudentResource($student->loadCount('classes'));
    }
}
