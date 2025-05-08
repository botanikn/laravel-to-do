<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskTag;
use Illuminate\Support\Facades\DB;
use App\Constants\HttpStatus;

class TaskTagController extends Controller
{
    // Добавить тег задаче
    public function addTagToTask(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'task_id' => 'required|integer',
            'tag_id' => 'required|integer'
        ]);

        $task = $user->tasks()->find($request->task_id);

        if (!$task) {
            return response()->json(['message' => 'Задача не найдена или нет доступа'], HttpStatus::NOT_FOUND);
        }

        // Проверка на дублирование связи
        $exists = TaskTag::where('task_id', $request->task_id)
            ->where('tag_id', $request->tag_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Тэг уже привязан к задаче'], HttpStatus::BAD_REQUEST);
        }

        $tag = TaskTag::create([
            'task_id' => $request->task_id,
            'tag_id' => $request->tag_id
        ]);

        return response()->json(["success" => true], HttpStatus::CREATED);
    }

    // Найти все задачи, к которым привязан тэг (по id)
    public function findTasksByTagId(Request $request, $id)
    {
        $tasks = DB::table('task_tag')
            ->join('tasks', 'tasks.id', '=', 'task_tag.task_id')
            ->select('tasks.title')
            ->where('task_tag.tag_id', '=', $id)
            ->groupBy('tasks.title')
            ->get();

        return response()->json($tasks, HttpStatus::OK);
    }

    // Удалить тег у задачи (только владелец задачи)
    public function deleteTagFromTask(Request $request)
    {
        $user = $request->user();

        $task = $user->tasks()->find($request->task_id);

        if (!$task) {
            return response()->json(['message' => 'Задача не найдена или нет доступа'], HttpStatus::NOT_FOUND);
        }

        $taskTag = TaskTag::find($request->id);

        $taskTag->delete();

        return response()->json(["success" => true], HttpStatus::OK);
    }
}
