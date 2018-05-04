<?php

use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Post endpoints: /posts/*
|--------------------------------------------------------------------------
|
*/

$router->prefix('posts')->middleware(['api-auth'])->group(function () use ($router) {
    $router->get('/', [
        'as'   => 'posts.list',
        'uses' => PostController::class.'@list',
    ]);

    $router->get('/own', [
        'as'   => 'posts.list.own',
        'uses' => PostController::class.'@listOwn',
    ]);

    $router->post('/', [
        'as'   => 'posts.create',
        'uses' => PostController::class.'@create',
    ]);
});
