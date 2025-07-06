<?php

namespace App\Repositories;

use App\Models\Task;
//use Illuminate\Database\Eloquent\Collection;
use App\DTOs\TaskDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator
    {
        $query = Task::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority_id'])) {
            $query->where('priority_id', $filters['priority_id']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['due_date_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_date_from']);
        }

        if (!empty($filters['due_date_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        if (!empty($filters['sort_by'])) {
            $direction = $filters['sort_direction'] ?? 'asc';
            if (
                in_array($filters['sort_by'], ['priority', 'category', 'due_date', 'created_at']) &&
                in_array($direction, ['asc', 'desc'])
            ) {
                $sortColumn = match ($filters['sort_by']) {
                    'priority' => 'priority_id',
                    'category' => 'category_id',
                    default => $filters['sort_by']
                };

                $query->orderBy($sortColumn, $direction);
            }
        }


        $perPage = isset($filters['per_page']) && is_numeric($filters['per_page'])
            ? intval($filters['per_page']) : 10;

        return $query->paginate($perPage);
    }

    public function find(int $id)
    {
        return Task::find($id);
    }

    public function create(TaskDTO $taskDTO)
    {
        return Task::create($taskDTO->toArray());
    }

    public function update(int $id, TaskDTO $taskDTO)
    {
        $task = Task::findOrFail($id);
        $task->update($taskDTO->toArray());
        return $task;
    }

    public function delete(int $id): bool
    {
        $task = Task::findOrFail($id);
        return $task->delete();
    }

    public function updateStatus(int $id, string $status)
    {
        $task = Task::findOrFail($id);
        $task->status = $status;
        $task->save();

        return $task;
    }
}
