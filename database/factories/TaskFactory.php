<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(1),
            'status' => fake()->randomElement(['Pending', 'In Progress', 'Completed']),
            'due_date' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'), // random date within last and next month
            // 'user_id' => User::factory(), // Create a new user for each task
        ];
    }
}
