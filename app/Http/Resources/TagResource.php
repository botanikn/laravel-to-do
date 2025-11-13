<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->statusCode);
        $response->header('Content-Type', 'application/json');
    }
}
