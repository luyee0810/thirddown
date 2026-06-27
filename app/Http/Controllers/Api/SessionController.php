<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * List the coach's sessions with attendance status.
     */
    public function index(Request $request): JsonResponse
    {
        $sessions = ClassSession::query()
            ->whereHas('trainingClass', fn ($q) => $q->where('coach_id', $request->user()->id))
            ->with(['trainingClass' => fn ($q) => $q->withCount('students')])
            ->withCount('attendances')
            ->orderBy('session_date')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'class_id' => $s->class_id,
                'class_name' => $s->trainingClass->name,
                'session_date' => $s->session_date->toDateString(),
                'start_time' => $s->start_time,
                'end_time' => $s->end_time,
                'status' => $s->status,
                'roster_size' => $s->trainingClass->students_count,
                'marked_count' => $s->attendances_count,
                'is_marked' => $s->attendances_count > 0,
            ]);

        return response()->json(['data' => $sessions]);
    }
}
