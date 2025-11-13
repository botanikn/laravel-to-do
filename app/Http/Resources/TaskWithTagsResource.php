<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskWithTagsResource extends JsonResource
{
    protected $statusCode;

    public function __construct($resource, $statusCode)
    {
        parent::__construct($resource);
        $this->statusCode = $statusCode;
    }
    public function toArray($request): array
    {
        $tags = $this->tags ?? [];

        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'text'        => $this->text,
            'tags'        => TagResource::collection(collect($tags))
            // TODO: Возможно добавить сюда timestamps как в TagResource
        ];
    }
    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->statusCode);
        $response->header('Content-Type', 'application/json');
    }

}
