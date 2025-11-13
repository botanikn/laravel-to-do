<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Constants\HttpStatus;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\TaskWithTagsResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {}

    /**
     * Get all tasks for the authenticated user
     * @param Request $request
     * @return JsonResponse|ErrorResource
     */
    public function index(Request $request): JsonResponse|ErrorResource
    {
        try {
            $tasks = $this->taskService->getUserTasks($request->user());
            return TaskWithTagsResource::collection($tasks)
                ->response()
                ->setStatusCode(HttpStatus::OK);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store new task
     * @param TaskRequest $request
     * @return TaskWithTagsResource|ErrorResource
     */
    public function store(TaskRequest $request): TaskWithTagsResource|ErrorResource
    {
        try {
            $task = $this->taskService->createTask(
                $request->user(),
                $request->validated()
            );

            // TODO: Возможно сделать возвращение только id созданной task
            return new TaskWithTagsResource($task, HttpStatus::CREATED);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get one task by id
     * @param Request $request
     * @param int $id
     * @return TaskWithTagsResource|ErrorResource
     */
    public function show(Request $request, int $id): TaskWithTagsResource|ErrorResource
    {
        try {
            $task = $this->taskService->findTask($request->user(), $id);
            if (!$task) {
                // TODO: Возможно вынести данный функционал в другой класс, например, ResponseHelper
                return $this->taskNotFoundResponse();
            }

            return new TaskWithTagsResource($task, HttpStatus::OK);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified task
     * @param TaskRequest $request
     * @param int $id
     * @return TaskWithTagsResource|ErrorResource
     */
    public function update(TaskRequest $request, int $id): TaskWithTagsResource|ErrorResource
    {
        try {
            $task = $this->taskService->findTask($request->user(), $id);
            if (!$task) {
                return $this->taskNotFoundResponse();
            }

            if (!$this->taskService->updateTask($task, $request->validated())) {
                return new ErrorResource('Failed to update task', HttpStatus::INTERNAL_SERVER_ERROR);
            }
            $task->refresh();

            return new TaskWithTagsResource($task, HttpStatus::OK);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove task
     * @param Request $request
     * @param int $id
     *
     */
    public function destroy(Request $request, int $id): SuccessResource|ErrorResource
    {
        try {
            $task = $this->taskService->findTask($request->user(), $id);

            if (!$task) {
                return $this->taskNotFoundResponse();
            }

            if (!$this->taskService->deleteTask($task)) {
                return new ErrorResource('Failed to delete task', HttpStatus::INTERNAL_SERVER_ERROR);
            }

            return new SuccessResource('Task deleted successfully', HttpStatus::OK);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Return a standardized "task not found" response
     * @return ErrorResource
     */
    private function taskNotFoundResponse(): ErrorResource
    {
        return new ErrorResource('Task not found or access denied', HttpStatus::NOT_FOUND);
    }
}
