<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Collection;

class TagService
{
    public function getUserTags($user): Collection
    {
        return $user->tags()->get();
    }

    public function createTag($user, array $data): Tag
    {
        return $user->tags()->create([
            'title' => $data['title'],
        ]);
    }

    public function findTag($user, $id): ?Tag
    {
        return $user->tags()->find($id);
    }

    public function updateTag(Tag $tag, array $data): bool
    {
        return $tag->update($data);
    }

    public function deleteTag(Tag $tag): bool
    {
        return $tag->delete();
    }
} 