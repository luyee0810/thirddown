<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::query()
            ->withCount('classes')
            ->with('parent')
            ->orderBy('first_name')
            ->get();

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        return view('admin.students.create');
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('students', 'public');
        }

        $student = Student::create($data + ['is_active' => true]);

        return redirect()
            ->route('admin.students.index')
            ->with('status', "{$student->full_name} added.");
    }

    public function edit(Student $student): View
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(StoreStudentRequest $request, Student $student): RedirectResponse
    {
        $data = $request->safe()->except('photo');

        if ($request->hasFile('photo')) {
            if ($student->photo_path && ! str_starts_with($student->photo_path, 'http')) {
                Storage::disk('public')->delete($student->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($data);

        return redirect()
            ->route('admin.students.index')
            ->with('status', "{$student->full_name} updated.");
    }

    public function destroy(Student $student): RedirectResponse
    {
        $name = $student->full_name;

        if ($student->photo_path && ! str_starts_with($student->photo_path, 'http')) {
            Storage::disk('public')->delete($student->photo_path);
        }

        // Enrollments and attendance rows cascade on delete at the DB level.
        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('status', "{$name} deleted.");
    }
}
