<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\AuthenticateUser;
use App\Http\Requests\Auth\RegisterUser;
use App\Http\Serializers\UserSerializer;
use App\Models\User;
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
     * Generate a JWT token response.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function tokenResponse(string $token): JsonResponse
    {
        return response()->jsonApiSpec([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->auth->factory()->getTTL() * 60,
        ], 201);
    }

    /**
     * Register a User.
     *
     * @param RegisterUser $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUser $request): JsonResponse
    {
        $user = new User();

        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = $request->get('password');

        $user->save();

        return response()->resource($user, new UserSerializer(), [], 201);
    }

    /**
     * Authenticate a User.
     *
     * @param AuthenticateUser $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(AuthenticateUser $request): JsonResponse
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

        return $this->tokenResponse($token);
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

    /**
     * Refresh the current User's token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->tokenResponse($this->auth->refresh());
    }
}
