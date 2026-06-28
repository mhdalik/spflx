<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Create 5 fake tasks for each user who don't have any tasks yet
        User::all()->each(function ($user) {
            if (Task::where('user_id', $user->id)->count() == 0) {
                Task::factory()->count(5)->create(['user_id' => $user->id]);
            }
        });
    }
}
