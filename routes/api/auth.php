<?php

use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Auth endpoints: /auth/*
|--------------------------------------------------------------------------
|
*/

$router->prefix('auth')->group(function () use ($router) {
    $router->post('/login', [
        'as'   => 'auth.login',
        'uses' => AuthController::class.'@login',
    ]);
});
