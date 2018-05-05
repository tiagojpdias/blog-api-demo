<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the User can list Users.
     *
     * @param User $user
     *
     * @return bool
     */
    public function list(User $user): bool
    {
        return true;
    }
}
