<?php
namespace App\DTOs;

class TaskDTO
{
    public string $title;
    public ?string $description;
    public ?string $due_date;
    public ?int $priority_id;
    public ?int $category_id;
    public ?int $user_id;
    public ?string $status;

    public function __construct(array $data)
    {
        $this->title = $data['title'];
        $this->description = $data['description'] ?? null;
        $this->due_date = $data['due_date'] ?? null;
        $this->priority_id = $data['priority_id'] ?? null;
        $this->category_id = $data['category_id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->status = $data['status'] ?? null;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
