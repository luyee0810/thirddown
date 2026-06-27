<?php

namespace App\Http\Controllers;

use App\Actions\SaveAttendance;
use App\Models\ClassSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Show the attendance-marking grid for a session.
     */
    public function edit(ClassSession $session): View
    {
        $class = $session->trainingClass;
        abort_unless($class->coach_id === Auth::id(), 403);

        $students = $class->students()->orderBy('first_name')->get();
        $existing = $session->attendances()->pluck('status', 'student_id');

        return view('attendance.edit', compact('session', 'class', 'students', 'existing'));
    }

    /**
     * Save attendance for the session.
     */
    public function update(Request $request, ClassSession $session, SaveAttendance $save): RedirectResponse
    {
        abort_unless($session->trainingClass->coach_id === Auth::id(), 403);

        $data = $request->validate([
            'attendance' => ['required', 'array'],
            'attendance.*' => ['in:present,absent,late,excused'],
        ]);

        $save->execute($session, $data['attendance'], Auth::id());

        return redirect()
            ->route('classes.show', $session->trainingClass)
            ->with('status', 'Attendance saved.');
    }
}
