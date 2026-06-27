<?php

namespace App\Http\Controllers\Api;

use App\Actions\SaveAttendance;
use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Roster for a session with each student's current attendance status.
     */
    public function index(Request $request, ClassSession $session): JsonResponse
    {
        $class = $session->trainingClass;
        abort_unless($class->coach_id === $request->user()->id, 403);

        $existing = $session->attendances()->pluck('status', 'student_id');

        $roster = $class->students()->orderBy('first_name')->get()->map(fn ($s) => [
            'student_id' => $s->id,
            'full_name' => $s->full_name,
            'photo_url' => $s->photo_url,
            'age' => $s->date_of_birth?->age,
            'status' => $existing[$s->id] ?? null,
        ]);

        return response()->json([
            'session' => [
                'id' => $session->id,
                'class_id' => $class->id,
                'class_name' => $class->name,
                'session_date' => $session->session_date->toDateString(),
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
            ],
            'roster' => $roster,
        ]);
    }

    /**
     * Save attendance for a session.
     */
    public function store(Request $request, ClassSession $session, SaveAttendance $save): JsonResponse
    {
        abort_unless($session->trainingClass->coach_id === $request->user()->id, 403);

        $data = $request->validate([
            'attendance' => ['required', 'array'],
            'attendance.*' => ['in:present,absent,late,excused'],
        ]);

        $save->execute($session, $data['attendance'], $request->user()->id);

        return response()->json(['message' => 'Attendance saved.']);
    }
}
