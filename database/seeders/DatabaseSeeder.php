<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Default User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $project = Project::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Default Project'],
            ['description' => 'Proyek contoh dari seeder.']
        );

        Task::firstOrCreate(
            ['project_id' => $project->id, 'title' => 'Default Task'],
            ['status' => Task::STATUS_PENDING]
        );
    }
}
