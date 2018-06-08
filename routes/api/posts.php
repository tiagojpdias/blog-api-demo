<?php

use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Post endpoints: /posts/*
|--------------------------------------------------------------------------
|
*/

$router->prefix('posts')->middleware(['json-api', 'jwt-auth'])->group(function () use ($router) {
    $router->get('/', [
        'as'   => 'posts::list',
        'uses' => PostController::class.'@list',
    ]);

    $router->get('/own', [
        'as'   => 'posts::list::own',
        'uses' => PostController::class.'@listOwn',
    ]);

    $router->post('/', [
        'as'   => 'posts::create',
        'uses' => PostController::class.'@create',
    ]);

    $router->get('/{post}', [
        'as'   => 'posts::read',
        'uses' => PostController::class.'@read',
    ]);

    $router->put('/{post}', [
        'as'   => 'posts::update',
        'uses' => PostController::class.'@update',
    ]);

    $router->delete('/{post}', [
        'as'   => 'posts::delete',
        'uses' => PostController::class.'@delete',
    ]);
});
