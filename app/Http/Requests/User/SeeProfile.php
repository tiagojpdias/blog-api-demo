<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\Models\User;

class SeeProfile extends Request
{
    /**
     * {@inheritdoc}
     */
    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }
}
