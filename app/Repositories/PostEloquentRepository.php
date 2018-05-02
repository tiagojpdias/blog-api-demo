<?php

namespace App\Repositories;

use App\Filters\PostFilter;
use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostEloquentRepository implements PostRepository
{
    /**
     * {@inheritdoc}
     */
    public function getPaginator(PostFilter $filter): LengthAwarePaginator
    {
        $queryBuilder = Post::query();

        $filter->applyTo($queryBuilder);

        return $queryBuilder->paginate(
            $filter->getItemsPerPage(),
            $filter->getColumns(),
            'page',
            $filter->getPageNumber()
        );
    }
}
