<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateProfile extends Request
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            'name' => [
                'max:255',
            ],
            'email' => [
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->user()->id, 'email'),
            ],
            'password' => [
                'confirmed',
            ],
        ];
    }
}
