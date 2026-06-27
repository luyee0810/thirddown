<?php

namespace Tests\Feature\Api;

use App\Models\Student;
use App\Models\TrainingClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function coach(): User
    {
        return User::create([
            'name' => 'Coach', 'email' => 'coach@test.test', 'password' => 'password',
            'role' => 'coach', 'is_active' => true,
        ]);
    }

    public function test_login_returns_a_token(): void
    {
        $this->coach();

        $this->postJson('/api/login', [
            'email' => 'coach@test.test',
            'password' => 'password',
            'device_name' => 'iphone',
        ])
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
    }

    public function test_login_rejects_bad_credentials(): void
    {
        $this->coach();

        $this->postJson('/api/login', ['email' => 'coach@test.test', 'password' => 'nope'])
            ->assertStatus(422);
    }

    public function test_protected_routes_require_a_token(): void
    {
        $this->getJson('/api/classes')->assertUnauthorized();
    }

    public function test_create_single_class_via_api(): void
    {
        Sanctum::actingAs($this->coach());

        $this->postJson('/api/classes', [
            'name' => 'API Clinic', 'type' => 'single', 'session_date' => '2026-07-01',
        ])
            ->assertCreated()
            ->assertJsonPath('data.type', 'single')
            ->assertJsonCount(1, 'data.sessions');
    }

    public function test_create_regular_class_generates_sessions_via_api(): void
    {
        Sanctum::actingAs($this->coach());

        $this->postJson('/api/classes', [
            'name' => 'API Wednesdays', 'type' => 'regular',
            'start_date' => '2026-07-01', 'end_date' => '2026-07-31', 'weekdays' => [3],
        ])
            ->assertCreated()
            ->assertJsonCount(5, 'data.sessions');
    }

    public function test_create_student_and_list_via_api(): void
    {
        Sanctum::actingAs($this->coach());

        $this->postJson('/api/students', ['first_name' => 'Sam', 'last_name' => 'Lee'])
            ->assertCreated()
            ->assertJsonPath('data.full_name', 'Sam Lee');

        $this->getJson('/api/students')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_assign_students_via_api(): void
    {
        $coach = $this->coach();
        Sanctum::actingAs($coach);

        $class = TrainingClass::create(['name' => 'Squad', 'type' => 'single', 'coach_id' => $coach->id, 'status' => 'active']);
        $student = Student::create(['first_name' => 'A', 'last_name' => 'One']);

        $this->postJson("/api/classes/{$class->id}/students", ['student_ids' => [$student->id]])
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->assertDatabaseHas('enrollments', ['class_id' => $class->id, 'student_id' => $student->id]);
    }

    public function test_cannot_view_another_coachs_class_via_api(): void
    {
        $other = User::create(['name' => 'Other', 'email' => 'o@test.test', 'password' => 'x', 'role' => 'coach']);
        $class = TrainingClass::create(['name' => 'Theirs', 'type' => 'single', 'coach_id' => $other->id, 'status' => 'active']);

        Sanctum::actingAs($this->coach());

        $this->getJson("/api/classes/{$class->id}")->assertForbidden();
    }
}
