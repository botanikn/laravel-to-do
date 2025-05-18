<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    protected $statusCode;
    protected $message;

    public function __construct($message, $statusCode)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    public function toArray($request): array
    {
        return [
            "success" => false,
            "message" => $this->message
        ];
    }

    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->statusCode);
        $response->header('Content-Type', 'application/json');
    }
}
