<?php

namespace Tests\Feature;

use App\Models\User;

trait ApiUser
{
    /**
     * API User.
     *
     * @var User
     */
    private $user;

    /**
     * Get an API User instance.
     *
     * @return User
     */
    private function getApiUser(): User
    {
        if ($this->user) {
            return $this->user;
        }

        return $this->user = factory(User::class)->create();
    }

    /**
     * Generate an API User token.
     *
     * @return string
     */
    private function generateApiUserToken(): string
    {
        return auth()->tokenById($this->getApiUser()->id);
    }
}
