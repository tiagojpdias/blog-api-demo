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

        // Apply sorting
        if ($this->sortColumn) {
            switch ($this->sortColumn) {
                case 'name':
                    $queryBuilder->orderBy($this->column($this->sortColumn), $this->sortOrder);
                    break;

                default:
                    $queryBuilder->orderBy('users.id', $this->sortOrder);
                    break;
            }
        }
    }
}
