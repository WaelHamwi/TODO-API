<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Priority;
use App\Models\Category;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

   
    protected function createUserWithRole(string $roleName = 'Owner'): User
    {
        $user = User::factory()->create();

        $role = Role::firstOrCreate(['name' => $roleName]);

        $user->roles()->syncWithoutDetaching([$role->id]);

        return $user;
    }

    public function test_user_can_list_tasks()
    {
        $user = $this->createUserWithRole('Owner');
        $priority = Priority::factory()->create();
        $category = Category::factory()->create();

        Task::factory()->create([
            'user_id' => $user->id,
            'priority_id' => $priority->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonStructure(['data', 'meta']);
    }

    public function test_user_can_create_task()
    {
        $user = $this->createUserWithRole('Owner');
        $priority = Priority::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user, 'api');

        $data = [
            'title' => 'Test Task',
            'description' => 'Test Desc',
            'due_date' => now()->addDays(3)->toDateString(),
            'priority_id' => $priority->id,
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Task']);
    }

    public function test_user_can_view_task()
    {
        $user = $this->createUserWithRole('Owner');
        $priority = Priority::factory()->create();
        $category = Category::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'priority_id' => $priority->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user, 'api');

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertOk()->assertJsonFragment(['id' => $task->id]);
    }

    public function test_user_can_update_task()
    {
        $user = $this->createUserWithRole('Owner');
        $priority = Priority::factory()->create();
        $category = Category::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'priority_id' => $priority->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user, 'api');

        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'priority_id' => $task->priority_id,
            'category_id' => $task->category_id,
            'user_id' => $task->user_id,
            'status' => $task->status,
            'due_date' => $task->due_date, 
            'description' => $task->description,
        ]);

        $response->assertOk()->assertJsonFragment(['title' => 'Updated Title']);
    }

    public function test_user_can_delete_task()
    {
        $user = $this->createUserWithRole('Owner');
        $priority = Priority::factory()->create();
        $category = Category::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'priority_id' => $priority->id,
            'category_id' => $category->id,
        ]);

        $this->actingAs($user, 'api');

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertOk()->assertJson(['message' => 'Task deleted successfully']);
    }

    public function test_user_can_update_status()
    {
        $user = $this->createUserWithRole('Owner');
        $priority = Priority::factory()->create();
        $category = Category::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'priority_id' => $priority->id,
            'category_id' => $category->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user, 'api');

        $response = $this->patchJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed'
        ]);

        $response->assertOk()->assertJsonFragment(['status' => 'completed']);
    }
}
