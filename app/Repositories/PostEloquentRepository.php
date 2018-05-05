<?php

namespace App\Repositories;

use App\Filters\PostFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostEloquentRepository implements PostRepository
{
    /**
     * {@inheritdoc}
     */
    public function getPaginator(PostFilter $filter, $queryBuilder): LengthAwarePaginator
    {
        $filter->applyTo($queryBuilder);

        return $queryBuilder->paginate(
            $filter->getItemsPerPage(),
            $filter->getColumns(),
            'page',
            $filter->getPageNumber()
        );
    }
}
