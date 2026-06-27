<?php

namespace Database\Seeders;

use App\Actions\CreateClass;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $createClass = app(CreateClass::class);

        $coach = User::where('email', 'coach@thirddown.test')->first()
            ?? User::factory()->create(['role' => 'coach', 'is_active' => true]);

        // A roster of students.
        $students = Student::factory(8)->create();

        // Class blueprints — dated relative to today so some sessions are past, some upcoming.
        $blueprints = [
            [
                'name' => 'U12 Wednesdays', 'type' => 'regular', 'location' => 'Field 1',
                'start_date' => now()->subWeeks(3)->toDateString(),
                'end_date' => now()->addWeeks(3)->toDateString(),
                'weekdays' => [3], 'start_time' => '17:00', 'end_time' => '18:30',
            ],
            [
                'name' => 'U14 Mon & Thu', 'type' => 'regular', 'location' => 'Field 2',
                'start_date' => now()->subWeeks(3)->toDateString(),
                'end_date' => now()->addWeeks(2)->toDateString(),
                'weekdays' => [1, 4], 'start_time' => '18:00', 'end_time' => '19:30',
            ],
            [
                'name' => 'School Holiday Clinic', 'type' => 'single', 'location' => 'Main Pitch',
                'session_date' => now()->subDays(6)->toDateString(), 'start_time' => '09:00', 'end_time' => '12:00',
            ],
            [
                'name' => 'Goalkeeping Masterclass', 'type' => 'single', 'location' => 'Field 3',
                'session_date' => now()->addDays(5)->toDateString(), 'start_time' => '10:00', 'end_time' => '11:30',
            ],
        ];

        $statuses = ['present', 'present', 'present', 'present', 'late', 'absent', 'excused'];

        foreach ($blueprints as $data) {
            $class = $createClass->execute($data, $coach->id);

            // Enroll a random subset of the roster.
            $enrollees = $students->random(rand(5, $students->count()));
            $pivot = $enrollees->mapWithKeys(fn ($s) => [
                $s->id => ['enrolled_at' => now()->subWeeks(4)->toDateString(), 'status' => 'active'],
            ])->all();
            $class->students()->syncWithoutDetaching($pivot);

            // Mark attendance for sessions that have already happened.
            foreach ($class->sessions as $session) {
                if ($session->session_date->isFuture()) {
                    continue;
                }

                foreach ($enrollees as $student) {
                    Attendance::create([
                        'class_session_id' => $session->id,
                        'student_id' => $student->id,
                        'status' => $statuses[array_rand($statuses)],
                        'marked_by' => $coach->id,
                        'marked_at' => $session->session_date->copy()->setTime(18, 0),
                    ]);
                }
            }
        }
    }
}
