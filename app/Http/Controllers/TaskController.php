<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use App\Constants\HttpStatus;
use App\Http\Resources\TaskWithTagsResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\ErrorResource;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
    // Получить все задачи текущего пользователя
    public function index(Request $request)
    {
        $user = $request->user();
        $tasks = $user->tasks()->get();
        
        // Находим теги для всех задач одним запросом
        $taskIds = $tasks->pluck('id');
        $tagsByTask = DB::table('task_tag')
            ->join('tags', 'tags.id', '=', 'task_tag.tag_id')
            ->whereIn('task_tag.task_id', $taskIds)
            ->select(
                'tags.title as tag', 
                'tags.id as tag_id', 
                'task_tag.task_id', 
                'task_tag.id as id'
            )
            ->get()
            ->groupBy('task_id');
        
        // Добавляем теги к задачам
        $tasks->each(function ($task) use ($tagsByTask) {
            $task->tags = $tagsByTask->get($task->id, []);
        });
        
        return TaskWithTagsResource::collection($tasks)
            ->response()
            ->setStatusCode(HttpStatus::OK);
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

        // Получаем задачу пользователя по ID
        $task = $user->tasks()->find($id);

        if (!$task) {
            return new ErrorResource(message: "Задача не найдена или нет доступа", statusCode: HttpStatus::NOT_FOUND);
        }

        // Получаем теги для этой задачи
        $tags = DB::table('task_tag')
            ->join('tags', 'tags.id', '=', 'task_tag.tag_id')
            ->where('task_tag.task_id', $id)
            ->select(
                'tags.title as tag',
                'tags.id as tag_id',
                'task_tag.task_id',
                'task_tag.id as id'
            )
            ->get();

        // Добавляем теги к задаче
        $task->tags = $tags;

        return (new TaskWithTagsResource($task))
            ->response()
            ->setStatusCode(HttpStatus::OK);
    }


    // Обновить задачу
    public function update(TaskRequest $request, $id)
    {
        $user = $request->user();

        $task = $user->tasks()->find($id);

        if (!$task) {
            return new ErrorResource(message: "Задача не найдена или нет доступа", statusCode: HttpStatus::NOT_FOUND);
        }

        try {
            $task->update($request->only('title', 'text'));
        }
        catch (\Exception $e) {
            return new ErrorResource(message: $e->errors(), statusCode: HttpStatus::NOT_FOUND);
        }

        return new TaskResource($task, statusCode: HttpStatus::OK);
    }

    // Удалить задачу (только владелец)
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $task = $user->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Задача не найдена или нет доступа'], HttpStatus::NOT_FOUND);
        }

        $bounds = DB::table('task_tag')
            ->where('task_tag.task_id', "=", $task->id)
            ->select('task_tag.*')
            ->delete();

        $task->delete();

        return response()->json(['message' => 'Задача удалена'], HttpStatus::OK);
    }
}
