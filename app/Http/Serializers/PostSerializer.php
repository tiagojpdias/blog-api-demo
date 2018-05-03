<?php

namespace App\Http\Serializers;

use App\Models\Post;
use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\Resource;

class PostSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'posts';

    /**
     * {@inheritdoc}
     */
    public function getAttributes($model, array $fields = null): array
    {
        return [
            'title'        => $model->title,
            'slug'         => $model->slug,
            'content'      => $model->content,
            'published_at' => $model->published_at ? $model->published_at->toDateTimeString() : null,
            'created_at'   => $model->created_at->toDateTimeString(),
            'updated_at'   => $model->updated_at->toDateTimeString(),
        ];
    }

    /**
     * Post Author.
     *
     * @param Post $post
     *
     * @return \Tobscure\JsonApi\Relationship
     */
    public function author(Post $post): Relationship
    {
        return new Relationship(new Resource($post->author, new UserSerializer()));
    }
}
