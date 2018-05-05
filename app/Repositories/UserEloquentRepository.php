<?php

namespace App\Repositories;

use App\Filters\UserFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserEloquentRepository implements UserRepository
{
    /**
     * {@inheritdoc}
     */
    public function getPaginator(UserFilter $filter, $queryBuilder): LengthAwarePaginator
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
