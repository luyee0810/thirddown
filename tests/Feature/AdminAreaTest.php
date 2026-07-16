<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\TrainingClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAreaTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'a@test.test', 'password' => 'password',
            'role' => 'admin', 'is_active' => true,
        ]);
    }

    private function coach(string $email = 'c@test.test'): User
    {
        return User::create([
            'name' => 'Coach', 'email' => $email, 'password' => 'password',
            'role' => 'coach', 'is_active' => true,
        ]);
    }

    public function test_admin_lands_on_admin_dashboard(): void
    {
        $admin = $this->admin();
        $this->assertSame('admin.dashboard', $admin->homeRoute());
        $this->actingAs($admin)->get('/admin')->assertOk()->assertSee('Site administration');
    }

    public function test_all_admin_pages_render(): void
    {
        $admin = $this->admin();
        $coach = $this->coach();
        $class = TrainingClass::create(['name' => 'U12', 'type' => 'single', 'coach_id' => $coach->id, 'status' => 'active']);
        $student = Student::create(['first_name' => 'Sam', 'last_name' => 'Lee', 'is_active' => true]);

        foreach ([
            '/admin', '/admin/users', '/admin/users?role=coach', '/admin/users/create',
            "/admin/users/{$coach->id}/edit", '/admin/students', '/admin/students/create',
            "/admin/students/{$student->id}/edit", '/admin/classes',
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_coach_cannot_access_admin_area(): void
    {
        $this->actingAs($this->coach())->get('/admin')->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_create_a_coach(): void
    {
        $this->actingAs($this->admin())->post('/admin/users', [
            'name' => 'New Coach', 'email' => 'new@test.test', 'role' => 'coach',
            'password' => 'password123', 'password_confirmation' => 'password123', 'is_active' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'new@test.test', 'role' => 'coach']);
    }

    public function test_deleting_coach_with_classes_is_blocked_until_reassigned(): void
    {
        $admin = $this->admin();
        $coach = $this->coach();
        $other = $this->coach('other@test.test');
        $class = TrainingClass::create([
            'name' => 'U12', 'type' => 'single', 'coach_id' => $coach->id, 'status' => 'active',
        ]);

        // Blocked.
        $this->actingAs($admin)->delete("/admin/users/{$coach->id}")->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $coach->id]);

        // Reassign, then delete succeeds.
        $this->actingAs($admin)->put("/admin/users/{$coach->id}/reassign-classes", ['new_coach_id' => $other->id]);
        $this->assertDatabaseHas('classes', ['id' => $class->id, 'coach_id' => $other->id]);

        $this->actingAs($admin)->delete("/admin/users/{$coach->id}")->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $coach->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_class_and_cascades(): void
    {
        $coach = $this->coach();
        $class = TrainingClass::create([
            'name' => 'U14', 'type' => 'single', 'coach_id' => $coach->id, 'status' => 'active',
        ]);

        $this->actingAs($this->admin())->delete("/admin/classes/{$class->id}")->assertRedirect();
        $this->assertDatabaseMissing('classes', ['id' => $class->id]);
    }

    public function test_admin_can_delete_student(): void
    {
        $student = Student::create(['first_name' => 'Sam', 'last_name' => 'Lee', 'is_active' => true]);
        $this->actingAs($this->admin())->delete("/admin/students/{$student->id}")->assertRedirect();
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }
}
