<?php

namespace App\Http\Serializers;

use App\Models\User;
use Tobscure\JsonApi\AbstractSerializer;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Relationship;

class UserSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'users';

    /**
     * {@inheritdoc}
     */
    public function getAttributes($model, array $fields = null): array
    {
        return [
            'name'       => $model->name,
            'email'      => $model->email,
            'created_at' => $model->created_at->toDateTimeString(),
            'updated_at' => $model->updated_at->toDateTimeString(),
        ];
    }

    /**
     * Blog Posts.
     *
     * @param User $user
     *
     * @return \Tobscure\JsonApi\Relationship
     */
    public function posts(User $user): Relationship
    {
        return new Relationship(new Collection($user->posts, new PostSerializer()));
    }
}
