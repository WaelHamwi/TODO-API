<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\DTOs\TaskDTO;

interface TaskRepositoryInterface
{
    public function all(array $filters): LengthAwarePaginator;

    public function find(int $id);

    public function create(TaskDTO $taskDTO);

    public function update(int $id, TaskDTO $taskDTO);

    public function delete(int $id): bool;
    public function updateStatus(int $id, string $status);
}
