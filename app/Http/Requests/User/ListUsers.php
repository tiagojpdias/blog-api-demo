<?php

namespace App\Http\Requests\User;

use App\Filters\UserEloquentFilter;
use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class ListUsers extends Request
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        return $this->user()->can('list', User::class);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            'page' => [
                'integer',
            ],
            'per_page' => [
                'integer',
            ],
            'search' => [
                'string',
            ],
            'sort' => [
                Rule::in(UserEloquentFilter::validSortColumns()),
            ],
            'order' => [
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],
        ];
    }
}
