<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\User;
use App\Repositories\TagRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TagService
{
    private $tagRepository;
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }
    public function getUserTags(User $user): Collection
    {
        return $this->tagRepository->getUserTags($user);
    }

    public function createTag(User $user, array $data): Model
    {
        return $this->tagRepository->createTag($user, $data);
    }

    public function findTag(User $user, int $id): ?Tag
    {
        return $this->tagRepository->findUserTag($user, $id);
    }

    public function updateTag(Tag $tag, array $data): bool
    {
        return $this->tagRepository->updateTag($tag, $data);
    }

    public function deleteTag(Tag $tag): bool
    {
        return $this->tagRepository->deleteTag($tag);
    }
}
