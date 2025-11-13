<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\Tag;

class TagRepository
{
    /**
     * Get all users tags
     * @param User $user
     * @return Collection
     */
    public function getUserTags(User $user): Collection
    {
        return $user->tags()->get();
    }

    /**
     * Create new tag
     * @param User $user
     * @param array $data
     * @return Model
     */
    public function createTag(User $user, array $data): Model
    {
        return $user->tags()->create([
            'title' => $data['title'],
        ]);
    }

    /**
     * Find one tag by it id
     * @param User $user
     * @param int $id
     * @return Model|null
     */
    public function findUserTag(User $user, int $id): ?Model
    {
        return $user->tags()->find($id);
    }

    /**
     * Update tag
     * @param Tag $tag
     * @param array $data
     * @return bool
     */
    public function updateTag(Tag $tag, array $data): bool
    {
        return $tag->update($data);
    }

    /**
     * Delete tag
     * @param Tag $tag
     * @return bool
     */
    public function deleteTag(Tag $tag): bool
    {
        return $tag->delete();
    }
}
