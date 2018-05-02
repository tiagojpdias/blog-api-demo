<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function (User $user) {
            factory(Post::class, random_int(2, 16))->create([
                'author_id' => $user->id,
            ]);
        });
    }
}
