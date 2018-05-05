<?php

namespace App\Http\Requests\Post;

use App\Filters\PostEloquentFilter;
use App\Http\Requests\Request;
use App\Models\Post;
use Illuminate\Validation\Rule;

class ListPosts extends Request
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        return $this->user()->can('list', Post::class);
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
            'authors.*' => [
                Rule::exists('users', 'id'),
            ],
            'sort' => [
                Rule::in(PostEloquentFilter::validSortColumns()),
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
