<?php

namespace App\Repositories;

use App\Filters\UserFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepository
{
    /**
     * Get a User paginator.
     *
     * @param UserFilter $filter
     * @param mixed      $queryBuilder
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginator(UserFilter $filter, $queryBuilder): LengthAwarePaginator;
}
