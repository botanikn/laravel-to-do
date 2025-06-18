<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Constants\HttpStatus;
use App\Http\Resources\TaskWithTagsResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Http\Requests\TaskRequest;
use App\Services\TaskService;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    // Получить все задачи текущего пользователя
    public function index(Request $request)
    {
        $tasks = $this->taskService->getUserTasks($request->user());
        return TaskWithTagsResource::collection($tasks)
            ->response()
            ->setStatusCode(HttpStatus::OK);
    }

    // Создать новую задачу
    public function store(TaskRequest $request)
    {
        $task = $this->taskService->createTask($request->user(), $request->only(['title', 'text']));
        return new TaskResource($task, statusCode: HttpStatus::CREATED);
    }

    // Найти по id
    public function findById(Request $request, $id)
    {
        $task = $this->taskService->findTask($request->user(), $id);

        if (!$task) {
            return new ErrorResource(message: "Задача не найдена или нет доступа", statusCode: HttpStatus::NOT_FOUND);
        }

        return (new TaskWithTagsResource($task))
            ->response()
            ->setStatusCode(HttpStatus::OK);
    }

    // Обновить задачу
    public function update(TaskRequest $request, $id)
    {
        $task = $this->taskService->findTask($request->user(), $id);

        if (!$task) {
            return new ErrorResource(message: "Задача не найдена или нет доступа", statusCode: HttpStatus::NOT_FOUND);
        }

        $this->taskService->updateTask($task, $request->only('title', 'text'));
        return new TaskResource($task, statusCode: HttpStatus::OK);
    }

    // Удалить задачу (только владелец)
    public function destroy(Request $request, $id)
    {
        $task = $this->taskService->findTask($request->user(), $id);

        if (!$task) {
            return new ErrorResource(message: 'Задача не найдена или нет доступа', statusCode: HttpStatus::NOT_FOUND);
        }

        $this->taskService->deleteTask($task);
        return new SuccessResource(message: 'Задача удалена', statusCode: HttpStatus::OK);
    }
}
