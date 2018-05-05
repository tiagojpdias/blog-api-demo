<?php

namespace App\Http\Requests\Post;

use App\Filters\PostEloquentFilter;
use App\Http\Requests\Request;
use App\Models\Post;
use Illuminate\Validation\Rule;

class ListOwnPosts extends Request
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        return $this->user()->can('listOwn', Post::class);
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
            'published' => [
                'nullable',
                'boolean',
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
