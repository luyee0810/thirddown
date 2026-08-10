<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'parent_name',
        'parent_email',
        'parent_phone',
        'notes',
        'photo_path',
        'is_active',
        'credits',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
            'credits' => 'integer',
        ];
    }

    /**
     * The parent (user account) who registered this student, if any.
     *
     * @return BelongsTo<User, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Classes this student is enrolled in.
     *
     * @return BelongsToMany<TrainingClass, $this>
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(TrainingClass::class, 'enrollments', 'student_id', 'class_id')
            ->withPivot(['enrolled_at', 'status'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return HasMany<Attendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Age in whole years, derived from the date of birth so it stays current
     * without any stored value to refresh. Null when no date of birth is set.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    /**
     * Public URL for the student's photo, or null to fall back to a default icon.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo_path) {
            return null;
        }

        return str_starts_with($this->photo_path, 'http')
            ? $this->photo_path
            : Storage::url($this->photo_path);
    }
}
