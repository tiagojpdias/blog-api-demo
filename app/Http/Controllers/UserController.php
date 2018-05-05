<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\SeeProfile;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Serializers\UserSerializer;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Get the current User profile.
     *
     * @param SeeProfile $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(SeeProfile $request): JsonResponse
    {
        return response()->resource($request->user(), new UserSerializer());
    }

    /**
     * Update the current User's profile.
     *
     * @param UpdateProfile $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UpdateProfile $request): JsonResponse
    {
        $user = $request->user();

        if ($name = $request->get('name')) {
            $user->name = $name;
        }

        if ($email = $request->get('email')) {
            $user->email = $email;
        }

        if ($password = $request->get('password')) {
            $user->password = $password;
        }

        $user->save();

        return response()->resource($user, new UserSerializer());
    }
}
