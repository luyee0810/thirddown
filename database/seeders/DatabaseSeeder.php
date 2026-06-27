<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@thirddown.test'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role' => 'admin',
                'is_active' => true,
            ],
        );

        User::updateOrCreate(
            ['email' => 'coach@thirddown.test'],
            [
                'name' => 'Coach Leon',
                'password' => 'password',
                'role' => 'coach',
                'is_active' => true,
            ],
        );

        $this->call(DemoSeeder::class);
    }
}
