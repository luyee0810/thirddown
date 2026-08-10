<?php

namespace App\Models;

use Database\Factories\AttendanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    /** @use HasFactory<AttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        'class_session_id',
        'student_id',
        'status',
        'credit_charged',
        'marked_by',
        'marked_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'marked_at' => 'datetime',
            'credit_charged' => 'boolean',
        ];
    }

    /**
     * Statuses that consume a session credit. Absent and excused are free.
     *
     * @var array<string>
     */
    public const CHARGEABLE = ['present', 'late'];

    public function isChargeable(): bool
    {
        return in_array($this->status, self::CHARGEABLE, true);
    }

    /**
     * @return BelongsTo<ClassSession, $this>
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
