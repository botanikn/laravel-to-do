<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Constants\HttpStatus;
use App\Http\Resources\TagResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\SuccessResource;
use App\Services\TagService;
use App\Http\Requests\TagRequest;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Get all users tags
     * @param Request $request
     * @return JsonResponse|ErrorResource
     */
    public function index(Request $request): JsonResponse|ErrorResource
    {
        try {
            $tags = $this->tagService->getUserTags($request->user());
            return TagResource::collection($tags)
                ->response()
                ->setStatusCode(HttpStatus::OK);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store new tag
     * @param TagRequest $request
     * @return TagResource|ErrorResource
     */
    public function store(TagRequest $request): TagResource|ErrorResource
    {
        try {
            $tag = $this->tagService->createTag($request->user(), $request->title);
            return new TagResource($tag, HttpStatus::CREATED);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get one task by id
     * @param Request $request
     * @param int $id
     * @return TagResource|ErrorResource
     */
    public function show(Request $request, int $id): TagResource|ErrorResource
    {
        try {
            $tag = $this->tagService->findTag($request->user(), $id);

            if (!$tag) {
                $this->tagNotFoundResponse();
            }

            return new TagResource($tag, HttpStatus::OK);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update one task by id
     * @param TagRequest $request
     * @param int $id
     * @return TagResource|ErrorResource
     */
    public function update(TagRequest $request, int $id): TagResource|ErrorResource
    {
        try {
            $tag = $this->tagService->findTag($request->user(), $id);

            if (!$tag) {
                $this->tagNotFoundResponse();
            }

            if (!$this->tagService->updateTag($tag, $request->only('title'))) {
                return new ErrorResource('Failed to update tag', HttpStatus::INTERNAL_SERVER_ERROR);
            }
            return new TagResource($tag, HttpStatus::OK);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete one task by id
     * @param Request $request
     * @param int $id
     * @return SuccessResource|ErrorResource
     */
    public function destroy(Request $request, int $id): SuccessResource|ErrorResource
    {
        try {
            $tag = $this->tagService->findTag($request->user(), $id);

            if (!$tag) {
                $this->tagNotFoundResponse();
            }

            if (!$this->tagService->deleteTag($tag)) {
                return new ErrorResource('Failed to delete tag', HttpStatus::INTERNAL_SERVER_ERROR);
            }
            return new SuccessResource('Тэг удалён', HttpStatus::OK);
        } catch (\Exception $e) {
            return new ErrorResource($e->getMessage(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Return a standardized "tag not found" response
     * @return ErrorResource
     */
    private function tagNotFoundResponse(): ErrorResource
    {
        return new ErrorResource('Tag not found or access denied', HttpStatus::NOT_FOUND);
    }
}
