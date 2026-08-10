<?php

namespace App\Actions;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Student;
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

                $attendance = Attendance::firstOrNew(
                    ['class_session_id' => $session->id, 'student_id' => $studentId],
                );

                $attendance->fill(['status' => $status, 'marked_by' => $markedBy, 'marked_at' => now()]);

                // A credit is spent only when the student actually trains
                // (present or late). Absent and excused are free, so switching
                // between the two kinds charges or refunds exactly once —
                // credit_charged is what keeps it from double-counting.
                // Balances may go negative.
                if ($attendance->isChargeable() && ! $attendance->credit_charged) {
                    Student::whereKey($studentId)->decrement('credits');
                    $attendance->credit_charged = true;
                } elseif (! $attendance->isChargeable() && $attendance->credit_charged) {
                    Student::whereKey($studentId)->increment('credits');
                    $attendance->credit_charged = false;
                }

                $attendance->save();
            }
        });
    }
}
