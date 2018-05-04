<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\Request;

class CreatePost extends Request
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
            'title' => [
                'required',
                'max:255',
            ],
            'content' => [
                'required',
                'string',
            ],
            'published_at' => [
                'date',
            ],
        ];
    }
}
