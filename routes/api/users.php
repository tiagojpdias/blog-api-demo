<?php

use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| User endpoints: /users/*
|--------------------------------------------------------------------------
|
*/

$router->prefix('users')->middleware(['json-api', 'jwt-auth'])->group(function () use ($router) {
    $router->get('/', [
        'as'   => 'users.list',
        'uses' => UserController::class.'@list',
    ]);

    $router->get('/profile', [
        'as'   => 'users.profile',
        'uses' => UserController::class.'@profile',
    ]);

    $router->put('/profile', [
        'as'   => 'users.profile.update',
        'uses' => UserController::class.'@updateProfile',
    ]);
});
