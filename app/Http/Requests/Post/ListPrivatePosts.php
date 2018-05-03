<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ListPrivatePosts extends Request
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        return true;
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
            'items' => [
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
                Rule::in([
                    'id',
                    'title',
                    'slug',
                    'content',
                    'published_at',
                    'created_at',
                    'updated_at',
                ]),
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
