<?php

namespace App\Http\Serializers;

use Tobscure\JsonApi\AbstractSerializer;

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
}
