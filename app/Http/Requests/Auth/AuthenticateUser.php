<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class AuthenticateUser extends Request
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
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ];
    }
}
