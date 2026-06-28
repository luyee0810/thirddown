<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_register_and_land_on_parent_dashboard(): void
    {
        $res = $this->post('/register', [
            'name' => 'Pat Parent',
            'email' => 'pat@parent.test',
            'phone' => '555',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertRedirect(route('parent.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'pat@parent.test', 'role' => 'parent']);
    }

    public function test_parent_can_add_a_child(): void
    {
        $parent = User::create(['name' => 'P', 'email' => 'p@p.test', 'password' => 'password', 'role' => 'parent', 'is_active' => true]);

        $this->actingAs($parent)->post('/parent/children', [
            'first_name' => 'Kid', 'last_name' => 'One',
        ])->assertRedirect(route('parent.dashboard'));

        $this->assertDatabaseHas('students', ['first_name' => 'Kid', 'parent_id' => $parent->id]);
    }

    public function test_parent_cannot_edit_another_parents_child(): void
    {
        $a = User::create(['name' => 'A', 'email' => 'a@p.test', 'password' => 'password', 'role' => 'parent', 'is_active' => true]);
        $b = User::create(['name' => 'B', 'email' => 'b@p.test', 'password' => 'password', 'role' => 'parent', 'is_active' => true]);
        $kid = $a->students()->create(['first_name' => 'K', 'last_name' => 'B', 'is_active' => true]);

        $this->actingAs($b)->get("/parent/children/{$kid->id}/edit")->assertForbidden();
    }

    public function test_coach_cannot_access_parent_area_and_parent_cannot_access_dashboard(): void
    {
        $coach = User::create(['name' => 'C', 'email' => 'c@c.test', 'password' => 'password', 'role' => 'coach', 'is_active' => true]);
        $parent = User::create(['name' => 'P', 'email' => 'p2@p.test', 'password' => 'password', 'role' => 'parent', 'is_active' => true]);

        $this->actingAs($coach)->get('/parent')->assertRedirect(route('dashboard'));
        $this->actingAs($parent)->get('/dashboard')->assertRedirect(route('parent.dashboard'));
    }
}
