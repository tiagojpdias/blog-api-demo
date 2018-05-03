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
        'as'   => 'posts.list.published',
        'uses' => PostController::class.'@listPublished',
    ]);

    $router->get('/my', [
        'as'   => 'posts.list.private',
        'uses' => PostController::class.'@listPrivate',
    ]);
});
