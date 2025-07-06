<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }


    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'status',
            'priority_id',
            'category_id',
            'due_date_from',
            'due_date_to',
            'search',
        ]);

        $tasks = $this->taskService->getAllTasks($filters);

        return response()->json([
            'data' => TaskResource::collection($tasks),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        $data = $request->only([
            'title',
            'description',
            'due_date',
            'priority_id',
            'category_id',
            'user_id',
            'status',
        ]);

        $task = $this->taskService->createTask($data);

        return response()->json(new TaskResource($task), 201);
    }

    public function show(int $id): JsonResponse
    {
        $task = $this->taskService->getTaskById($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json(new TaskResource($task));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->only([
            'title',
            'description',
            'due_date',
            'priority_id',
            'category_id',
            'user_id',
            'status',
        ]);

        try {
            $task = $this->taskService->updateTask($id, $data);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json(new TaskResource($task));
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->taskService->deleteTask($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        if ($deleted) {
            return response()->json(['message' => 'Task deleted successfully']);
        }

        return response()->json(['message' => 'Failed to delete task'], 500);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);

        try {
            $task = $this->taskService->updateTaskStatus($id, $request->status);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json(new TaskResource($task));
    }
}
