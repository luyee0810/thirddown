<?php

namespace Tests\Feature;

use App\Models\ClassSession;
use App\Models\Student;
use App\Models\TrainingClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class StudentCreditTest extends TestCase
{
    use RefreshDatabase;

    private User $coach;

    private TrainingClass $class;

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
        $this->student = Student::create(['first_name' => 'Mia', 'last_name' => 'Kim', 'credits' => 2]);
        $this->class->students()->attach($this->student->id, ['enrolled_at' => now(), 'status' => 'active']);
    }

    private function makeSession(string $date): ClassSession
    {
        return $this->class->sessions()->create([
            'session_date' => $date, 'start_time' => '09:00', 'end_time' => '10:30', 'status' => 'scheduled',
        ]);
    }

    public function test_marking_attendance_deducts_one_credit(): void
    {
        $session = $this->makeSession('2026-07-04');

        $this->actingAs($this->coach)->post(route('attendance.update', $session), [
            'attendance' => [$this->student->id => 'present'],
        ]);

        $this->assertSame(1, $this->student->fresh()->credits);
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function statusProvider(): array
    {
        return [
            'present charges' => ['present', 1],
            'late charges' => ['late', 1],
            'absent is free' => ['absent', 2],
            'excused is free' => ['excused', 2],
        ];
    }

    #[DataProvider('statusProvider')]
    public function test_only_attending_statuses_are_charged(string $status, int $expected): void
    {
        $session = $this->makeSession('2026-07-04');

        $this->actingAs($this->coach)->post(route('attendance.update', $session), [
            'attendance' => [$this->student->id => $status],
        ]);

        $this->assertSame($expected, $this->student->fresh()->credits);
    }

    public function test_re_saving_the_same_session_does_not_charge_twice(): void
    {
        $session = $this->makeSession('2026-07-04');

        $this->actingAs($this->coach)->post(route('attendance.update', $session), [
            'attendance' => [$this->student->id => 'present'],
        ]);
        $this->actingAs($this->coach)->post(route('attendance.update', $session), [
            'attendance' => [$this->student->id => 'late'],
        ]);

        $this->assertSame(1, $this->student->fresh()->credits);
    }

    public function test_changing_absent_to_present_charges_a_credit(): void
    {
        $session = $this->makeSession('2026-07-04');

        $this->actingAs($this->coach)->post(route('attendance.update', $session), [
            'attendance' => [$this->student->id => 'absent'],
        ]);
        $this->assertSame(2, $this->student->fresh()->credits);

        $this->actingAs($this->coach)->post(route('attendance.update', $session), [
            'attendance' => [$this->student->id => 'present'],
        ]);

        $this->assertSame(1, $this->student->fresh()->credits);
        $this->assertDatabaseHas('attendances', [
            'class_session_id' => $session->id,
            'student_id' => $this->student->id,
            'credit_charged' => true,
        ]);
    }

    public function test_changing_present_to_absent_refunds_the_credit(): void
    {
        $session = $this->makeSession('2026-07-04');

        $this->actingAs($this->coach)->post(route('attendance.update', $session), [
            'attendance' => [$this->student->id => 'present'],
        ]);
        $this->assertSame(1, $this->student->fresh()->credits);

        $this->actingAs($this->coach)->post(route('attendance.update', $session), [
            'attendance' => [$this->student->id => 'excused'],
        ]);

        $this->assertSame(2, $this->student->fresh()->credits);
        $this->assertDatabaseHas('attendances', [
            'class_session_id' => $session->id,
            'student_id' => $this->student->id,
            'credit_charged' => false,
        ]);
    }

    public function test_a_refund_happens_only_once(): void
    {
        $session = $this->makeSession('2026-07-04');
        $payloads = ['present', 'absent', 'absent', 'excused'];

        foreach ($payloads as $status) {
            $this->actingAs($this->coach)->post(route('attendance.update', $session), [
                'attendance' => [$this->student->id => $status],
            ]);
        }

        $this->assertSame(2, $this->student->fresh()->credits);
    }

    public function test_credits_can_go_negative(): void
    {
        foreach (['2026-07-04', '2026-07-11', '2026-07-18'] as $date) {
            $this->actingAs($this->coach)->post(route('attendance.update', $this->makeSession($date)), [
                'attendance' => [$this->student->id => 'present'],
            ]);
        }

        $this->assertSame(-1, $this->student->fresh()->credits);
    }

    public function test_unmarked_players_are_not_recorded_or_charged(): void
    {
        $session = $this->makeSession('2026-07-04');

        // An empty grid: nothing was tapped, so nothing is submitted.
        $this->actingAs($this->coach)
            ->post(route('attendance.update', $session), [])
            ->assertRedirect(route('classes.show', $this->class));

        $this->assertDatabaseCount('attendances', 0);
        $this->assertSame(2, $this->student->fresh()->credits);
    }

    public function test_coach_can_edit_remaining_credits(): void
    {
        $this->actingAs($this->coach)
            ->put(route('students.update', $this->student), [
                'first_name' => 'Mia', 'last_name' => 'Kim', 'credits' => -5,
            ])
            ->assertRedirect(route('students.show', $this->student));

        $this->assertSame(-5, $this->student->fresh()->credits);
    }

    public function test_blank_credits_leaves_the_balance_untouched(): void
    {
        $this->actingAs($this->coach)->put(route('students.update', $this->student), [
            'first_name' => 'Mia', 'last_name' => 'Kim', 'credits' => '',
        ]);

        $this->assertSame(2, $this->student->fresh()->credits);
    }

    public function test_non_numeric_credits_are_rejected(): void
    {
        $this->actingAs($this->coach)
            ->put(route('students.update', $this->student), [
                'first_name' => 'Mia', 'last_name' => 'Kim', 'credits' => 'lots',
            ])
            ->assertSessionHasErrors('credits');
    }
}
