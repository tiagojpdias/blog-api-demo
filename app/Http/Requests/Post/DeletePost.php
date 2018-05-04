<?php

namespace App\Http\Requests\Post;

use App\Http\Requests\Request;

class DeletePost extends Request
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        $post = $this->route('post');

        return $this->user()->can('delete', $post);
    }
}
