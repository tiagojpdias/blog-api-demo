<?php

use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Auth endpoints: /auth/*
|--------------------------------------------------------------------------
|
*/

$router->prefix('auth')->middleware(['json-api'])->group(function () use ($router) {
    $router->post('/register', [
        'as'   => 'auth.register',
        'uses' => AuthController::class.'@register',
    ]);

    $router->post('/authenticate', [
        'as'   => 'auth.authenticate',
        'uses' => AuthController::class.'@authenticate',
    ]);

    $router->put('/invalidate', [
        'as'   => 'auth.invalidate',
        'uses' => AuthController::class.'@invalidate',
    ])
    ->middleware(['jwt-auth']);

    $router->post('/refresh', [
        'as'   => 'auth.refresh',
        'uses' => AuthController::class.'@refresh',
    ])
    ->middleware(['jwt-auth']);
});
