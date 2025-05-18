<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'tag'    => $this->tag,
            'tag_id' => $this->tag_id,
            'id'     => $this->id,
        ];
    }
}
