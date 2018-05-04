<?php

namespace App\Http\Requests\Post;

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