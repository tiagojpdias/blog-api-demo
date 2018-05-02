<?php

use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Post endpoints: /posts/*
|--------------------------------------------------------------------------
|
*/

$router->prefix('posts')->group(function () use ($router) {
    $router->get('/', [
        'as'   => 'posts.list.published',
        'uses' => PostController::class.'@listPublished',
    ]);
});