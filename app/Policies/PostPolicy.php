<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the User can list published Articles.
     *
     * @param User $user
     *
     * @return bool
     */
    public function list(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the User can list his own Articles.
     *
     * @param User $user
     *
     * @return bool
     */
    public function listOwn(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the User can create Articles.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the User can update Articles.
     *
     * @param User $user
     * @param Post $article
     *
     * @return bool
     */
    public function update(User $user, Post $article): bool
    {
        // A Post can only be updated by its author
        return $article->author_id === $user->id;
    }

    /**
     * Determine whether the User can delete Articles.
     *
     * @param User $user
     * @param Post $article
     *
     * @return bool
     */
    public function delete(User $user, Post $article): bool
    {
        // A Post can only be deleted by its author
        return $article->author_id === $user->id;
    }
}
