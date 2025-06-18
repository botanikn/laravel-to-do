<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function getUserTasks($user): Collection
    {
        return $user->tasks()->with('tags')->get();
    }

    public function createTask($user, array $data): Task
    {
        return $user->tasks()->create([
            'title' => $data['title'],
            'text' => $data['text'],
        ]);
    }

    public function findTask($user, $id): ?Task
    {
        return $user->tasks()->with('tags')->find($id);
    }

    public function updateTask(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    public function syncTags(Task $task, array $tagIds): void
    {
        $task->tags()->sync($tagIds);
    }

    public function attachTag(Task $task, Tag $tag): void
    {
        $task->tags()->attach($tag->id);
    }

    public function detachTag(Task $task, Tag $tag): void
    {
        $task->tags()->detach($tag->id);
    }
} 