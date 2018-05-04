<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\Request;

class UpdatePost extends Request
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        $post = $this->route('post');

        return $this->user()->can('update', $post);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            'title' => [
                'max:255',
            ],
            'content' => [
                'string',
            ],
            'published_at' => [
                'date',
            ],
        ];
    }
}
