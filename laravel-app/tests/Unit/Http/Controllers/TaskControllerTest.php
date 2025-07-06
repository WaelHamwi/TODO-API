<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use Mockery;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\TaskService;
use App\Http\Controllers\TaskController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
class TaskControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_returns_paginated_tasks(): void
    {
        $taskServiceMock = Mockery::mock(TaskService::class);
        $controller = new TaskController($taskServiceMock);

        $items = collect([new Task(['id' => 1, 'title' => 'Test Task', 'status' => 'pending'])]);
        $paginator = new LengthAwarePaginator($items, 1, 10, 1);

        $filters = ['status' => 'pending'];

        $taskServiceMock->shouldReceive('getAllTasks')->once()->with($filters)->andReturn($paginator);

        $request = Request::create('/api/tasks?status=pending', 'GET', $filters);

        $response = $controller->index($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->status());
    }

    public function test_show_returns_404_if_not_found(): void
    {
        $taskServiceMock = Mockery::mock(TaskService::class);
        $controller = new TaskController($taskServiceMock);

        $taskServiceMock->shouldReceive('getTaskById')->once()->with(99)->andReturn(null);

        $response = $controller->show(99);

        $this->assertEquals(404, $response->status());
        $this->assertEquals(['message' => 'Task not found'], $response->getData(true));
    }

    public function test_store_creates_task(): void
    {
        $taskServiceMock = Mockery::mock(TaskService::class);
        $controller = new TaskController($taskServiceMock);

        $data = [
            'title' => 'Test',
            'description' => 'description',
            'due_date' => now()->toDateString(),
            'priority_id' => 1,
            'category_id' => 2,
            'user_id' => 3,
            'status' => 'pending',
        ];

        $request = Request::create('/api/tasks', 'POST', $data);

        $task = new Task($data);

        $taskServiceMock->shouldReceive('createTask')->once()->with($data)->andReturn($task);

        $response = $controller->store($request);

        $this->assertEquals(201, $response->status());
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_update_updates_existing_task(): void
    {
        $taskServiceMock = Mockery::mock(TaskService::class);
        $controller = new TaskController($taskServiceMock);

        $data = [
            'title' => 'Updated',
            'description' => 'Updated desc',
            'due_date' => now()->toDateString(),
            'priority_id' => 1,
            'category_id' => 2,
            'user_id' => 3,
            'status' => 'completed',
        ];

        $task = new Task($data);

        $request = Request::create('/api/tasks/1', 'PUT', $data);

        $taskServiceMock->shouldReceive('updateTask')->once()->with(1, $data)->andReturn($task);

        $response = $controller->update($request, 1);

        $this->assertEquals(200, $response->status());
    }

    public function test_update_returns_404_if_not_found(): void
    {
        $taskServiceMock = Mockery::mock(TaskService::class);
        $controller = new TaskController($taskServiceMock);

        $data = ['title' => 'Missing'];

        $request = Request::create('/api/tasks/1', 'PUT', $data);

        $taskServiceMock->shouldReceive('updateTask')->once()->with(1, $data)->andThrow(new ModelNotFoundException());

        $response = $controller->update($request, 1);

        $this->assertEquals(404, $response->status());
        $this->assertEquals(['message' => 'Task not found'], $response->getData(true));
    }

    public function test_destroy_successfully_deletes_task(): void
    {
        $taskServiceMock = Mockery::mock(TaskService::class);
        $controller = new TaskController($taskServiceMock);

        $taskServiceMock->shouldReceive('deleteTask')->once()->with(1)->andReturn(true);

        $response = $controller->destroy(1);

        $this->assertEquals(200, $response->status());
        $this->assertEquals(['message' => 'Task deleted successfully'], $response->getData(true));
    }

    public function test_destroy_returns_404_if_not_found(): void
    {
        $taskServiceMock = Mockery::mock(TaskService::class);
        $controller = new TaskController($taskServiceMock);

        $taskServiceMock->shouldReceive('deleteTask')->once()->with(99)->andThrow(new ModelNotFoundException());

        $response = $controller->destroy(99);

        $this->assertEquals(404, $response->status());
        $this->assertEquals(['message' => 'Task not found'], $response->getData(true));
    }


    

    public function test_update_status_returns_404_if_not_found(): void
    {
        $taskServiceMock = Mockery::mock(TaskService::class);
        $controller = new TaskController($taskServiceMock);

        $request = Request::create('/api/tasks/1/status', 'PATCH', ['status' => 'completed']);

        $taskServiceMock->shouldReceive('updateTaskStatus')->once()->with(1, 'completed')->andThrow(new ModelNotFoundException());

        $response = $controller->updateStatus($request, 1);

        $this->assertEquals(404, $response->status());
        $this->assertEquals(['message' => 'Task not found'], $response->getData(true));
    }
}
