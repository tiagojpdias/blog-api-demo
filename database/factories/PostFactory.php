<?php

use App\Models\Post;
use App\Models\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Post Factories
|--------------------------------------------------------------------------
|
*/

$factory->define(Post::class, function (Faker $faker) {
    return [
        'author_id'    => function () {
            return factory(User::class)->create()->id;
        },
        'title'        => $faker->unique()->sentence,
        'content'      => $faker->unique()->paragraphs(4, true),
        'published_at' => $faker->boolean ? $faker->dateTimeBetween('now', '+2 months') : null,
    ];
});
