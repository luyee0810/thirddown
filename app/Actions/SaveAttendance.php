<?php

namespace App\Actions;

use App\Models\Attendance;
use App\Models\ClassSession;
use Illuminate\Support\Facades\DB;

class SaveAttendance
{
    /**
     * Persist attendance for a session.
     *
     * @param  array<int|string, string>  $statuses  studentId => present|absent|late|excused
     */
    public function execute(ClassSession $session, array $statuses, int $markedBy): void
    {
        // Only enrolled students may be marked.
        $enrolledIds = $session->trainingClass->students()->pluck('students.id')->all();

        DB::transaction(function () use ($session, $statuses, $markedBy, $enrolledIds) {
            foreach ($statuses as $studentId => $status) {
                if (! in_array((int) $studentId, $enrolledIds, true)) {
                    continue;
                }

                Attendance::updateOrCreate(
                    ['class_session_id' => $session->id, 'student_id' => $studentId],
                    ['status' => $status, 'marked_by' => $markedBy, 'marked_at' => now()],
                );
            }
        });
    }
}
