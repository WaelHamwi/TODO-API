<?php

namespace App\Services;

use App\Repositories\TaskRepositoryInterface;
use App\DTOs\TaskDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
class TaskService
{
    protected TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getAllTasks(array $filters): LengthAwarePaginator
    {
        return $this->taskRepository->all($filters);
    }
    public function getTaskById(int $id)
    {
        return $this->taskRepository->find($id);
    }

    public function createTask(array $data)
    {
        $dto = new TaskDTO($data);
        return $this->taskRepository->create($dto);
    }

    public function updateTask(int $id, array $data)
    {
        $dto = new TaskDTO($data);
        return $this->taskRepository->update($id, $dto);
    }

    public function deleteTask(int $id)
    {
        return $this->taskRepository->delete($id);
    }
    public function updateTaskStatus(int $id, string $status)
    {
        return $this->taskRepository->updateStatus($id, $status);
    }
}
