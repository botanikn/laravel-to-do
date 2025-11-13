<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\User;
use App\Repositories\TagRepository;
use Illuminate\Support\Collection;

class TagService
{
    public function __construct(
        private readonly TagRepository $tagRepository,
    ) {}

    /**
     * Get all users tags
     * @param User $user
     * @return Collection
     */
    public function getUserTags(User $user): Collection
    {
        return $this->tagRepository->getUserTags($user);
    }

    /**
     * Create new tag
     * @param User $user
     * @param string $data
     * @return Tag
     */
    public function createTag(User $user, string $data): Tag
    {
        return $this->tagRepository->createTag($user, $data);
    }

    /**
     * Find one tag by it id
     * @param User $user
     * @param int $id
     * @return Tag|null
     */
    public function findTag(User $user, int $id): ?Tag
    {
        return $this->tagRepository->findUserTag($user, $id);
    }

    /**
     * Update tag
     * @param Tag $tag
     * @param array $data
     * @return bool
     */
    public function updateTag(Tag $tag, array $data): bool
    {
        return $this->tagRepository->updateTag($tag, $data);
    }

    /**
     * Delete tag
     * @param Tag $tag
     * @return bool
     */
    public function deleteTag(Tag $tag): bool
    {
        return $this->tagRepository->deleteTag($tag);
    }
}
