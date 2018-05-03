<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\Login;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @param Login $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Login $request): JsonResponse
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
