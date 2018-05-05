<?php

namespace App\Repositories;

use App\Filters\PostFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PostRepository
{
    /**
     * Get a Post paginator.
     *
     * @param PostFilter $filter
     * @param mixed      $queryBuilder
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginator(PostFilter $filter, $queryBuilder): LengthAwarePaginator;
}
