<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TaskRepository
{
    /**
     * Get all Users tasks with tags
     * @param User $user
     * @return Collection
     * */
    public function getUserTasks(User $user): Collection
    {
        return $user->tasks()->with('tags')->get();
    }

    /**
     * Create new task
     * @param User $user
     * @param array $data
     * @return Model
     */
    public function createTask(User $user, array $data): Model
    {
        return $user->tasks()->create([
            'title' => $data['title'],
            'text' => $data['text'],
        ]);
    }

    /**
     * Find task by task's id
     * @param User $user
     * @param int $taskId
     * @return Model|null
     */
    public function findUserTask(User $user, int $taskId): ?Model
    {
        return $user->tasks()->with('tags')->find($taskId);
    }

    /**
     * Update task by task's id
     * @param Task $task
     * @param array $data
     * @return bool
     */
    public function updateTask(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    /**
     * Delete task by it id
     * @param Task $task
     * @return bool|null
     */
    public function deleteTask(Task $task): ?bool
    {
        return $task->delete();
    }

    public function syncTags(Model $task, array $tagIds): void
    {
        $task->tags()->sync($tagIds);
    }
}
