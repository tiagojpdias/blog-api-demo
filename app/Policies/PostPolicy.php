<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the User can list published Posts.
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
     * Determine whether the User can list his own Posts.
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
     * Determine whether the User can create Posts.
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
     * Determine whether the User can update Posts.
     *
     * @param User $user
     * @param Post $post
     *
     * @return bool
     */
    public function update(User $user, Post $post): bool
    {
        // A Post can only be updated by its author
        return $post->author_id == $user->id;
    }

    /**
     * Determine whether the User can delete Posts.
     *
     * @param User $user
     * @param Post $post
     *
     * @return bool
     */
    public function delete(User $user, Post $post): bool
    {
        // A Post can only be deleted by its author
        return $post->author_id == $user->id;
    }
}
