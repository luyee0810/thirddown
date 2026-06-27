<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\TrainingClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SessionListTest extends TestCase
{
    use RefreshDatabase;

    private function coachWithSession(string $date): array
    {
        $coach = User::create([
            'name' => 'Coach', 'email' => 'coach@test.test', 'password' => 'password',
            'role' => 'coach', 'is_active' => true,
        ]);
        $class = TrainingClass::create([
            'name' => 'Flag Football', 'type' => 'single', 'coach_id' => $coach->id, 'status' => 'active',
        ]);
        $session = $class->sessions()->create(['session_date' => $date, 'status' => 'scheduled']);
        $student = Student::create(['first_name' => 'Mia', 'last_name' => 'Kim']);
        $class->students()->attach($student->id, ['enrolled_at' => now(), 'status' => 'active']);

        return [$coach, $session, $student];
    }

    public function test_web_sessions_page_lists_the_coachs_sessions(): void
    {
        [$coach] = $this->coachWithSession(now()->addDay()->toDateString());

        $this->actingAs($coach)
            ->get(route('sessions.index'))
            ->assertOk()
            ->assertSee('Flag Football')
            ->assertSee('Mark attendance');
    }

    public function test_api_sessions_reports_marked_status(): void
    {
        [$coach, $session, $student] = $this->coachWithSession(now()->subDay()->toDateString());

        Sanctum::actingAs($coach);
        $this->getJson('/api/sessions')
            ->assertOk()
            ->assertJsonPath('data.0.class_name', 'Flag Football')
            ->assertJsonPath('data.0.is_marked', false);

        $session->attendances()->create([
            'student_id' => $student->id, 'status' => 'present', 'marked_by' => $coach->id, 'marked_at' => now(),
        ]);

        $this->getJson('/api/sessions')->assertJsonPath('data.0.is_marked', true);
    }

    public function test_dashboard_shows_upcoming_sessions(): void
    {
        [$coach] = $this->coachWithSession(now()->addDays(2)->toDateString());

        $this->actingAs($coach)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Upcoming sessions')
            ->assertSee('Flag Football');
    }
}
