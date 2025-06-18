<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\HttpStatus;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TaskTagRequest;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;

class TaskTagController extends Controller
{
    // Добавить тег задаче
    public function addTagToTask(TaskTagRequest $request)
    {
        $user = $request->user();
        $task = $user->tasks()->find($request->task_id);

        if (!$task) {
            return new ErrorResource(message: 'Задача не найдена или нет доступа', statusCode: HttpStatus::NOT_FOUND);
        }

        if ($task->tags()->where('tags.id', $request->tag_id)->exists()) {
            return new ErrorResource(message: 'Тэг уже привязан к задаче', statusCode: HttpStatus::BAD_REQUEST);
        }

        $task->tags()->attach($request->tag_id);

        return new SuccessResource(message: 'Тэг успешно привязан к задаче', statusCode: HttpStatus::CREATED);
    }

    // Найти все задачи, к которым привязан тэг (по id)
    public function findTasksByTagId(Request $request, $id)
    {
        $tasks = DB::table('tag_task')
            ->join('tasks', 'tasks.id', '=', 'tag_task.task_id')
            ->select('tasks.title')
            ->where('tag_task.tag_id', '=', $id)
            ->groupBy('tasks.title')
            ->get();

        return response()->json($tasks, HttpStatus::OK);
    }

    // Удалить тег у задачи (только владелец задачи)
    public function deleteTagFromTask(TaskTagRequest $request)
    {
        $user = $request->user();
        $task = $user->tasks()->find($request->task_id);

        if (!$task) {
            return new ErrorResource(message: 'Задача не найдена или нет доступа', statusCode: HttpStatus::NOT_FOUND);
        }

        $task->tags()->detach($request->tag_id);

        return new SuccessResource(message: 'Тэг отвязан от задачи', statusCode: HttpStatus::OK);
    }
}
