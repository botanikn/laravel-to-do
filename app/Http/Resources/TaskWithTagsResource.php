<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskWithTagsResource extends JsonResource
{
    public function toArray($request)
    {
        $tags = $this->tags ?? [];

        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'text'        => $this->text,
            'tags'        => TagResource::collection(collect($tags))
        ];
    }
}
