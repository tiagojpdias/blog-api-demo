<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group posts::list::published
     * @test
     */
    public function itFailsToGetPublishedPostsDueToValidationErrors(): void
    {
        // Bypass API authentication
        $this->withoutMiddleware(Authenticate::class);

        $response = $this->json('GET', route('posts.list.published'), [
            'page'     => 'foo',
            'per_page' => 'bar',
            'search'   => 123,
            'sort'     => 'baz',
            'order'    => 'waz',
            'authors'  => [
                4,
                8,
                16,
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 'page',
                    'detail' => 'The page must be an integer.',
                ],
                [
                    'id'     => 'per_page',
                    'detail' => 'The per page must be an integer.',
                ],
                [
                    'id'     => 'search',
                    'detail' => 'The search must be a string.',
                ],
                [
                    'id'     => 'sort',
                    'detail' => 'The selected sort is invalid.',
                ],
                [
                    'id'     => 'order',
                    'detail' => 'The selected order is invalid.',
                ],
                [
                    'id'     => 'authors.0',
                    'detail' => 'The selected authors.0 is invalid.',
                ],
                [
                    'id'     => 'authors.1',
                    'detail' => 'The selected authors.1 is invalid.',
                ],
                [
                    'id'     => 'authors.2',
                    'detail' => 'The selected authors.2 is invalid.',
                ],
            ],
        ]);
    }

    /**
     * @group posts::list::published
     * @test
     */
    public function itSuccessfullyReturnsPublishedPosts(): void
    {
        // Bypass API authentication
        $this->withoutMiddleware(Authenticate::class);

        factory(Post::class, 20)->create();

        $response = $this->json('GET', route('posts.list.published'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => [
                        'title',
                        'slug',
                        'content',
                        'published_at',
                        'created_at',
                        'updated_at',
                    ],
                    'relationships' => [
                        'author' => [
                            'data' => [
                                'type',
                                'id',
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => [
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
            'meta' => [
                'per-page',
                'total',
            ],
        ]);
    }

    /**
     * @group posts::list::private
     * @test
     */
    public function itFailsToGetPrivatePostsDueToValidationErrors(): void
    {
        $user = factory(User::class)->create();
        $token = auth()->tokenById($user->id);

        $response = $this->json('GET', route('posts.list.private'), [
            'page'      => 'foo',
            'per_page'  => 'bar',
            'search'    => 123,
            'sort'      => 'baz',
            'order'     => 'waz',
            'published' => 7,
        ], [
            'Authorization' => sprintf('Bearer %s', $token),
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 'page',
                    'detail' => 'The page must be an integer.',
                ],
                [
                    'id'     => 'per_page',
                    'detail' => 'The per page must be an integer.',
                ],
                [
                    'id'     => 'search',
                    'detail' => 'The search must be a string.',
                ],
                [
                    'id'     => 'published',
                    'detail' => 'The published field must be true or false.',
                ],
                [
                    'id'     => 'sort',
                    'detail' => 'The selected sort is invalid.',
                ],
                [
                    'id'     => 'order',
                    'detail' => 'The selected order is invalid.',
                ],
            ],
        ]);
    }

    /**
     * @group posts::list::private
     * @test
     */
    public function itSuccessfullyReturnsPrivatePosts(): void
    {
        $user = factory(User::class)->create();
        $token = auth()->tokenById($user->id);

        factory(Post::class, 20)->create([
            'author_id' => $user->id,
        ]);

        $response = $this->json('GET', route('posts.list.private'), [], [
            'Authorization' => sprintf('Bearer %s', $token),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => [
                        'title',
                        'slug',
                        'content',
                        'published_at',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
            'meta' => [
                'per-page',
                'total',
            ],
        ]);
    }

    /**
     * @group posts::create
     * @test
     */
    public function itFailsToCreatePostDueToValidationErrors(): void
    {
        $user = factory(User::class)->create();
        $token = auth()->tokenById($user->id);

        $response = $this->json('POST', route('posts.create'), [
            'title'        => str_repeat('foo', 100),
            'published_at' => 123,
        ], [
            'Authorization' => sprintf('Bearer %s', $token),
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 'title',
                    'detail' => 'The title may not be greater than 255 characters.',
                ],
                [
                    'id'     => 'content',
                    'detail' => 'The content field is required.',
                ],
                [
                    'id'     => 'published_at',
                    'detail' => 'The published at is not a valid date.',
                ],
            ],
        ]);
    }

    /**
     * @group posts::create
     * @test
     */
    public function itSuccessfullyCreatesPost(): void
    {
        $user = factory(User::class)->create();
        $token = auth()->tokenById($user->id);

        $response = $this->json('POST', route('posts.create'), [
            'title'        => 'Article Title',
            'content'      => 'Article content.',
            'published_at' => '2000-01-02 00:11:22',
        ], [
            'Authorization' => sprintf('Bearer %s', $token),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'title',
                    'slug',
                    'content',
                    'published_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
