<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\Authenticate;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Authentication.
     *
     * @var Auth
     */
    private $auth;

    /**
     * Auth controller constructor.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

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
            'expires_in'   => $this->auth->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Invalidate the current User's token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function invalidate(): JsonResponse
    {
        $this->auth->logout();

        return response()->jsonApiSpec([
            'meta' => [
                'info' => 'The token has been invalidated',
            ],
        ]);
    }
}
