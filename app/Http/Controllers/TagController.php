<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
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

    // Получить все теги
    public function index(Request $request)
    {
        $tags = $this->tagService->getUserTags($request->user());
        return TagResource::collection($tags)
            ->response()
            ->setStatusCode(HttpStatus::OK);
    }

    // Создать новый тег
    public function store(TagRequest $request)
    {
        $tag = $this->tagService->createTag($request->user(), $request->only('title'));
        return (new TagResource($tag))
            ->response()
            ->setStatusCode(HttpStatus::CREATED);
    }

    // Найти по id
    public function findById(Request $request, $id)
    {
        $tag = $this->tagService->findTag($request->user(), $id);

        if (!$tag) {
            return new ErrorResource(message: 'Тэг не найден', statusCode: HttpStatus::NOT_FOUND);
        }

        return (new TagResource($tag))
            ->response()
            ->setStatusCode(HttpStatus::OK);
    }

    // Обновить тег
    public function update(TagRequest $request, $id)
    {
        $tag = $this->tagService->findTag($request->user(), $id);

        if (!$tag) {
            return new ErrorResource(message: 'Тэг не найден или нет доступа', statusCode: HttpStatus::NOT_FOUND);
        }

        $this->tagService->updateTag($tag, $request->only('title'));
        return (new TagResource($tag))
            ->response()
            ->setStatusCode(HttpStatus::OK);
    }

    // Удалить тег
    public function destroy(Request $request, $id)
    {
        $tag = $this->tagService->findTag($request->user(), $id);

        if (!$tag) {
            return new ErrorResource(message: 'Тэг не найден или нет доступа', statusCode: HttpStatus::NOT_FOUND);
        }

        $this->tagService->deleteTag($tag);
        return new SuccessResource(message: 'Тэг удалён', statusCode: HttpStatus::OK);
    }
}
