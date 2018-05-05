<?php

namespace App\Filters;

class UserEloquentFilter extends AbstractEloquentFilter implements UserFilter
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'users';

    /**
     * {@inheritdoc}
     */
    protected $sortColumn = 'id';

    /**
     * {@inheritdoc}
     */
    public static function validSortColumns(): array
    {
        return [
            'id',
            'name',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function applyTo($queryBuilder): void
    {
        parent::applyTo($queryBuilder);

        // Apply search pattern filter
        if ($this->patterns) {
            $queryBuilder->where(function ($query) {
                foreach ($this->patterns as $pattern) {
                    $query->orWhere('users.name', 'LIKE', '%'.$pattern.'%');
                }
            });
        }
    }
}
