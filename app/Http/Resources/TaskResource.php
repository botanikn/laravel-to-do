<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    protected $statusCode;

    public function __construct($resource, $statusCode)
    {
        parent::__construct($resource);
        $this->statusCode = $statusCode;
    }

    public function toArray(Request $request): array
    {
        return [
            "title" => $this->title,
            "text" => $this->text,
            "user_id" => $this->user_id,
            "id" => $this->id
        ];
    }

    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->statusCode);
        $response->header('Content-Type', 'application/json');
    }
}
