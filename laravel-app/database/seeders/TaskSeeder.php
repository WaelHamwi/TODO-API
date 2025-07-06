<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\Priority;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // demo owner user
        $categories = Category::all();
        $priorities = Priority::all();

        if (!$user || $categories->isEmpty() || $priorities->isEmpty()) {
            $this->command->info('Make sure users, categories, and priorities are seeded first.');
            return;
        }

        Task::create([
            'title' => 'Finish Laravel API',
            'description' => 'Complete the task management API with JWT auth.',
            'due_date' => now()->addDays(5),
            'priority_id' => $priorities->where('name', 'High')->first()->id,
            'category_id' => $categories->where('name', 'Work')->first()->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Task::create([
            'title' => 'Buy groceries',
            'description' => 'Milk, Bread, Cheese, Eggs',
            'due_date' => now()->addDay(),
            'priority_id' => $priorities->where('name', 'Medium')->first()->id,
            'category_id' => $categories->where('name', 'Shopping')->first()->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }
}
