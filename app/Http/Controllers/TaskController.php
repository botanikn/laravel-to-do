<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use App\Constants\HttpStatus;

class TaskController extends Controller
{
    // Получить все задачи текущего пользователя
    public function index(Request $request)
    {
        $user = $request->user();
        $tasks = $user->tasks()->get();
        
        // Загружаем теги для всех задач одним запросом
        $taskIds = $tasks->pluck('id');
        $tagsByTask = DB::table('task_tag')
            ->join('tags', 'tags.id', '=', 'task_tag.tag_id')
            ->whereIn('task_tag.task_id', $taskIds)
            ->select('tags.title as tag', 'tags.id as tag_id', 'task_tag.task_id', 'task_tag.id as id')
            ->get()
            ->groupBy('task_id');
        
        // Добавляем теги к задачам
        $tasks->each(function ($task) use ($tagsByTask) {
            $task->tags = $tagsByTask->get($task->id, []);
        });
        
        return response()->json($tasks, HttpStatus::OK);
    }

    // Создать новую задачу
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|min:3|max:20',
                'text' => 'required|string|max:200',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'ошибка валидации',
                'errors' => $e->errors()
            ], HttpStatus::BAD_REQUEST);
        }

        $user = $request->user();

        $task = $user->tasks()->create([
            'title' => $request->title,
            'text' => $request->text,
        ]);

        return response()->json($task, HttpStatus::CREATED);
    }

    // Найти по id
    public function findById(Request $request, $id)
    {
        $user = $request->user();

        $task = $user->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found or access denied'], HttpStatus::NOT_FOUND);
        }

        return response()->json($task);
    }

    // Обновить задачу (только владелец)
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $task = $user->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found or access denied'], HttpStatus::NOT_FOUND);
        }

        try {
            $request->validate([
                'title' => 'required|string|min:3|max:20',
                'text' => 'required|string|max:200',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'ошибка валидации',
                'errors' => $e->errors()
            ], HttpStatus::BAD_REQUEST);
        }

        $task->update($request->only('title', 'text'));

        return response()->json($task, HttpStatus::OK);
    }

    // Удалить задачу (только владелец)
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $task = $user->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found or access denied'], HttpStatus::NOT_FOUND);
        }

        $bounds = DB::table('task_tag')
            ->where('task_tag.task_id', "=", $task->id)
            ->select('task_tag.*')
            ->delete();

        $task->delete();

        return response()->json(['message' => 'Task deleted'], HttpStatus::OK);
    }
}
