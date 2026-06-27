<?php

namespace Tests\Feature;

use App\Models\ClassSession;
use App\Models\Student;
use App\Models\TrainingClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $coach;

    private TrainingClass $class;

    private ClassSession $session;

    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->coach = User::create([
            'name' => 'Coach', 'email' => 'coach@test.test', 'password' => 'password',
            'role' => 'coach', 'is_active' => true,
        ]);
        $this->class = TrainingClass::create([
            'name' => 'Flag Football', 'type' => 'single', 'coach_id' => $this->coach->id, 'status' => 'active',
        ]);
        $this->session = $this->class->sessions()->create([
            'session_date' => '2026-07-04', 'start_time' => '09:00', 'end_time' => '10:30', 'status' => 'scheduled',
        ]);
        $this->student = Student::create(['first_name' => 'Mia', 'last_name' => 'Kim']);
        $this->class->students()->attach($this->student->id, ['enrolled_at' => now(), 'status' => 'active']);
    }

    public function test_coach_can_view_the_attendance_grid(): void
    {
        $this->actingAs($this->coach)
            ->get(route('attendance.edit', $this->session))
            ->assertOk()
            ->assertSee('Flag Football')
            ->assertSee('Mia Kim');
    }

    public function test_coach_can_save_attendance(): void
    {
        $this->actingAs($this->coach)
            ->post(route('attendance.update', $this->session), [
                'attendance' => [$this->student->id => 'absent'],
            ])
            ->assertRedirect(route('classes.show', $this->class));

        $this->assertDatabaseHas('attendances', [
            'class_session_id' => $this->session->id,
            'student_id' => $this->student->id,
            'status' => 'absent',
            'marked_by' => $this->coach->id,
        ]);
    }

    public function test_saving_again_updates_rather_than_duplicates(): void
    {
        $payload = ['attendance' => [$this->student->id => 'present']];
        $this->actingAs($this->coach)->post(route('attendance.update', $this->session), $payload);
        $this->actingAs($this->coach)->post(route('attendance.update', $this->session), [
            'attendance' => [$this->student->id => 'late'],
        ]);

        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', ['student_id' => $this->student->id, 'status' => 'late']);
    }

    public function test_non_enrolled_students_are_ignored(): void
    {
        $outsider = Student::create(['first_name' => 'Not', 'last_name' => 'Enrolled']);

        $this->actingAs($this->coach)->post(route('attendance.update', $this->session), [
            'attendance' => [$outsider->id => 'present'],
        ]);

        $this->assertDatabaseMissing('attendances', ['student_id' => $outsider->id]);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->actingAs($this->coach)
            ->post(route('attendance.update', $this->session), [
                'attendance' => [$this->student->id => 'maybe'],
            ])
            ->assertSessionHasErrors('attendance.'.$this->student->id);
    }

    public function test_other_coach_cannot_mark_attendance(): void
    {
        $other = User::create(['name' => 'Other', 'email' => 'o@test.test', 'password' => 'x', 'role' => 'coach']);

        $this->actingAs($other)
            ->get(route('attendance.edit', $this->session))
            ->assertForbidden();
    }

    public function test_api_returns_roster_with_statuses(): void
    {
        $this->session->attendances()->create([
            'student_id' => $this->student->id, 'status' => 'present', 'marked_by' => $this->coach->id, 'marked_at' => now(),
        ]);

        Sanctum::actingAs($this->coach);

        $this->getJson("/api/sessions/{$this->session->id}/attendance")
            ->assertOk()
            ->assertJsonPath('session.class_name', 'Flag Football')
            ->assertJsonPath('roster.0.full_name', 'Mia Kim')
            ->assertJsonPath('roster.0.status', 'present');
    }

    public function test_api_can_save_attendance(): void
    {
        Sanctum::actingAs($this->coach);

        $this->postJson("/api/sessions/{$this->session->id}/attendance", [
            'attendance' => [$this->student->id => 'excused'],
        ])->assertOk();

        $this->assertDatabaseHas('attendances', ['student_id' => $this->student->id, 'status' => 'excused']);
    }
}
