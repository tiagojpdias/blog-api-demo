<?php

namespace App\Http\Controllers;

use App\Filters\UserFilter;
use App\Http\Requests\User\ListUsers;
use App\Http\Requests\User\SeeProfile;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Serializers\UserSerializer;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * List Users.
     *
     * @param ListUsers      $request
     * @param UserFilter     $filter
     * @param UserRepository $userRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(ListUsers $request, UserFilter $filter, UserRepository $userRepository): JsonResponse
    {
        $filter->sortBy($request->input('sort', 'id'), $request->input('order', 'desc'))
            ->setItemsPerPage($request->input('per_page', 10))
            ->setPageNumber($request->input('page', 1));

        if ($search = $request->input('search')) {
            $filter->withSearchPattern($search);
        }

        $posts = $userRepository->getPaginator($filter, User::query());

        return response()->paginator($posts, new UserSerializer());
    }

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
