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
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginator(PostFilter $filter): LengthAwarePaginator;
}
