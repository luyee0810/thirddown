<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\TrainingClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private function coach(): User
    {
        return User::create([
            'name' => 'Coach', 'email' => 'coach@test.test', 'password' => 'password',
            'role' => 'coach', 'is_active' => true,
        ]);
    }

    private function class(User $coach, string $name): TrainingClass
    {
        return TrainingClass::create([
            'name' => $name, 'type' => 'single', 'coach_id' => $coach->id, 'status' => 'active',
        ]);
    }

    public function test_student_page_shows_enrolled_and_available_classes(): void
    {
        $coach = $this->coach();
        $in = $this->class($coach, 'In Class');
        $this->class($coach, 'Available Class');
        $student = Student::create(['first_name' => 'Haziq', 'last_name' => 'bin Ismail']);
        $in->students()->attach($student->id, ['enrolled_at' => now(), 'status' => 'active']);

        $this->actingAs($coach)
            ->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('In Class')
            ->assertSee('Available Class');
    }

    public function test_coach_can_enrol_a_student_into_multiple_classes_at_once(): void
    {
        $coach = $this->coach();
        $a = $this->class($coach, 'A');
        $b = $this->class($coach, 'B');
        $c = $this->class($coach, 'C');
        $student = Student::create(['first_name' => 'Mei', 'last_name' => 'Lee']);

        $this->actingAs($coach)
            ->post(route('students.classes.store', $student), ['class_ids' => [$a->id, $b->id, $c->id]])
            ->assertRedirect();

        $this->assertEqualsCanonicalizing(
            [$a->id, $b->id, $c->id],
            $student->classes()->pluck('classes.id')->all()
        );
    }

    public function test_enrol_only_affects_the_coachs_own_classes(): void
    {
        $coach = $this->coach();
        $mine = $this->class($coach, 'Mine');
        $other = $this->class(
            User::create(['name' => 'Other', 'email' => 'o@test.test', 'password' => 'x', 'role' => 'coach']),
            'Theirs'
        );
        $student = Student::create(['first_name' => 'Arjun', 'last_name' => 'a/l Muthu']);

        $this->actingAs($coach)
            ->post(route('students.classes.store', $student), ['class_ids' => [$mine->id, $other->id]]);

        $ids = $student->classes()->pluck('classes.id')->all();
        $this->assertContains($mine->id, $ids);
        $this->assertNotContains($other->id, $ids);
    }

    public function test_coach_can_remove_a_student_from_a_class_via_student_page(): void
    {
        $coach = $this->coach();
        $class = $this->class($coach, 'Squad');
        $student = Student::create(['first_name' => 'Deepa', 'last_name' => 'a/p Suppiah']);
        $class->students()->attach($student->id, ['enrolled_at' => now(), 'status' => 'active']);

        $this->actingAs($coach)
            ->delete(route('students.classes.destroy', [$student, $class]))
            ->assertRedirect();

        $this->assertCount(0, $student->fresh()->classes);
    }

    public function test_api_enrol_student_into_multiple_classes(): void
    {
        $coach = $this->coach();
        $a = $this->class($coach, 'A');
        $b = $this->class($coach, 'B');
        $student = Student::create(['first_name' => 'Kai', 'last_name' => 'Tan']);

        Sanctum::actingAs($coach);
        $this->postJson("/api/students/{$student->id}/classes", ['class_ids' => [$a->id, $b->id]])
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->assertCount(2, $student->fresh()->classes);
    }
}
