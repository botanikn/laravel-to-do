<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $statusCode;
    protected $message;

    public function __construct($resource, $message, $statusCode)
    {
        parent::__construct($resource);
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    public function toArray($request): array
    {
        return [
            "success" => true,
            "token" => $this->api_token,
            "message" => $this->message
        ];
    }

    public function withResponse($request, $response): void
    {
        $response->setStatusCode($this->statusCode);
        $response->header('Content-Type', 'application/json');
    }
}
