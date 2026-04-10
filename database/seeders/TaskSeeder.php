<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create tasks for the user
        Task::factory()->count(5)->pending()->create(['user_id' => $user->id]);
        Task::factory()->count(3)->inProgress()->create(['user_id' => $user->id]);
        Task::factory()->count(2)->completed()->create(['user_id' => $user->id]);
    }
}