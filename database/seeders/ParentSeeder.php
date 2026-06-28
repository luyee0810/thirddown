<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class ParentSeeder extends Seeder
{
    /**
     * Seed a couple of demo parent accounts, each with a few linked children,
     * so the parent login flow has something to show.
     */
    public function run(): void
    {
        $parents = [
            [
                'name' => 'Sarah Tan',
                'email' => 'parent@thirddown.test',
                'phone' => '012-345-6789',
                'children' => 2,
            ],
            [
                'name' => 'Daniel Lim',
                'email' => 'parent2@thirddown.test',
                'phone' => '019-876-5432',
                'children' => 3,
            ],
        ];

        foreach ($parents as $data) {
            $parent = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => 'password',
                    'role' => 'parent',
                    'is_active' => true,
                ],
            );

            // Only create children the first time this parent is seeded.
            if ($parent->students()->exists()) {
                continue;
            }

            Student::factory($data['children'])->create([
                'parent_id' => $parent->id,
                'parent_name' => $parent->name,
                'parent_email' => $parent->email,
                'parent_phone' => $parent->phone,
            ]);
        }
    }
}
