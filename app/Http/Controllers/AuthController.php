<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\Authenticate;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Authenticate a User.
     *
     * @param Authenticate $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Authenticate $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->jsonApiSpec([
                'errors' => [
                    'title'  => 'Unauthorised',
                    'detail' => 'Invalid credentials',
                ],
            ], 401);
        }

        return response()->jsonApiSpec([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
        ]);
    }
}
