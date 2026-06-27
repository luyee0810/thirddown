<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\TrainingClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function coach(): User
    {
        return User::create([
            'name' => 'Coach',
            'email' => 'coach@test.test',
            'password' => 'password',
            'role' => 'coach',
            'is_active' => true,
        ]);
    }

    public function test_coach_can_create_a_single_class(): void
    {
        $coach = $this->coach();

        $this->actingAs($coach)
            ->post(route('classes.store'), [
                'name' => 'One-off Clinic',
                'type' => 'single',
                'session_date' => '2026-07-01',
                'start_time' => '09:00',
                'end_time' => '10:30',
            ])
            ->assertRedirect();

        $class = TrainingClass::first();
        $this->assertSame('single', $class->type);
        $this->assertSame($coach->id, $class->coach_id);
        $this->assertCount(1, $class->sessions);
        $this->assertSame('2026-07-01', $class->sessions->first()->session_date->toDateString());
    }

    public function test_coach_can_create_a_regular_class_generating_weekly_sessions(): void
    {
        $coach = $this->coach();

        // Wednesdays (3) between 2026-07-01 (Wed) and 2026-07-31 -> 5 Wednesdays.
        $this->actingAs($coach)
            ->post(route('classes.store'), [
                'name' => 'U12 Wednesdays',
                'type' => 'regular',
                'start_date' => '2026-07-01',
                'end_date' => '2026-07-31',
                'weekdays' => [3],
                'start_time' => '17:00',
                'end_time' => '18:00',
            ])
            ->assertRedirect();

        $class = TrainingClass::first();
        $this->assertSame('regular', $class->type);
        $this->assertCount(5, $class->sessions);
    }

    public function test_coach_can_create_a_student(): void
    {
        $this->actingAs($this->coach())
            ->post(route('students.store'), [
                'first_name' => 'Sam',
                'last_name' => 'Lee',
                'parent_name' => 'Pat Lee',
            ])
            ->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('students', ['first_name' => 'Sam', 'last_name' => 'Lee']);
    }

    public function test_coach_can_assign_students_to_a_class(): void
    {
        $coach = $this->coach();
        $class = TrainingClass::create([
            'name' => 'Squad A', 'type' => 'single', 'coach_id' => $coach->id, 'status' => 'active',
        ]);
        $a = Student::create(['first_name' => 'A', 'last_name' => 'One']);
        $b = Student::create(['first_name' => 'B', 'last_name' => 'Two']);

        $this->actingAs($coach)
            ->post(route('classes.students.store', $class), ['student_ids' => [$a->id, $b->id]])
            ->assertRedirect();

        $this->assertCount(2, $class->fresh()->students);
        $this->assertDatabaseHas('enrollments', ['class_id' => $class->id, 'student_id' => $a->id, 'status' => 'active']);
    }

    public function test_coach_cannot_manage_another_coachs_class(): void
    {
        $other = TrainingClass::create([
            'name' => 'Not Mine', 'type' => 'single',
            'coach_id' => User::create(['name' => 'Other', 'email' => 'o@test.test', 'password' => 'x', 'role' => 'coach'])->id,
            'status' => 'active',
        ]);

        $this->actingAs($this->coach())
            ->get(route('classes.show', $other))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('classes.index'))->assertRedirect(route('login'));
    }
}
