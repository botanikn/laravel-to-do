<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository
    ) {}

    /**
     * Get all tasks for a user
     * @param User $user
     * @return Collection
     */
    public function getUserTasks(User $user): Collection
    {
        return $this->taskRepository->getUserTasks($user);
    }

    /**
     * Create a new task for a user
     * @param User $user
     * @param array $data
     * @return Task
     */
    public function createTask(User $user, array $data): Task
    {
        $task = $this->taskRepository->createTask($user, $data);

        if (isset($data['tags'])) {
            $this->taskRepository->syncTags($task, $data['tags']);
            $task->load('tags');
        }

        return $task;
    }

    /**
     * Find a task by ID for a specific user
     * @param User $user
     * @param int $taskId
     * @return Task|null
     */
    public function findTask(User $user, int $taskId): ?Task
    {
        return $this->taskRepository->findUserTask($user, $taskId);
    }

    /**
     * Update a task
     * @param Task $task
     * @param array $data
     * @return bool
     */
    public function updateTask(Task $task, array $data): bool
    {
        $updated = $this->taskRepository->updateTask($task, $data);

        if ($updated && isset($data['tags'])) {
            $this->taskRepository->syncTags($task, $data['tags']);
        }

        return $updated;
    }

    /**
     * Delete a task
     * @param Task $task
     * @return bool
     */
    public function deleteTask(Task $task): bool
    {
        return $this->taskRepository->deleteTask($task);
    }

    public function attachTagToTask(Task $task, int $tagId): void
    {
        $this->taskRepository->attachTagToTask($task, $tagId);
    }

    public function deleteTagFromTask(Task $task, int $tagId): void
    {
        $this->taskRepository->deleteTagFromTask($task, $tagId);
    }
}
