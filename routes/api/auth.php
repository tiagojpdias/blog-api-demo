<?php

use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Auth endpoints: /auth/*
|--------------------------------------------------------------------------
|
*/

$router->prefix('auth')->group(function () use ($router) {
    $router->post('/authenticate', [
        'as'   => 'auth.authenticate',
        'uses' => AuthController::class.'@authenticate',
    ]);
});
