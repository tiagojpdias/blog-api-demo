<?php

namespace Tests\Feature;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use ApiUser;
    use RefreshDatabase;

    /**
     * @group posts::list
     * @test
     */
    public function itFailsToGetPublishedPostsDueToValidationErrors(): void
    {
        $response = $this->json('GET', route('posts.list'), [
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
        ], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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
     * @group posts::list
     * @test
     */
    public function itSuccessfullyReturnsPublishedPosts(): void
    {
        $posts = factory(Post::class, 20)->create();

        $authors = $posts->map(function (Post $post) {
            return $post->author_id;
        });

        $response = $this->json('GET', route('posts.list'), [
            'page'     => 1,
            'per_page' => 50,
            'search'   => 'a',
            'sort'     => 'created_at',
            'order'    => 'asc',
            'authors'  => $authors,
        ], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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
     * @group posts::list::own
     * @test
     */
    public function itFailsToGetOwnPostsDueToValidationErrors(): void
    {
        $response = $this->json('GET', route('posts.list.own'), [
            'page'      => 'foo',
            'per_page'  => 'bar',
            'search'    => 123,
            'sort'      => 'baz',
            'order'     => 'waz',
            'published' => 7,
        ], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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
     * @group posts::list::own
     * @test
     */
    public function itSuccessfullyReturnsOwnPosts(): void
    {
        factory(Post::class, 20)->create([
            'author_id' => $this->getApiUser()->id,
        ]);

        $response = $this->json('GET', route('posts.list.own'), [
            'page'      => 2,
            'per_page'  => 5,
            'search'    => 'a',
            'sort'      => 'created_at',
            'order'     => 'asc',
            'published' => false,
        ], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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
     * @group posts::read
     * @test
     */
    public function itFailsToReadPostDueToNotFoundError(): void
    {
        $response = $this->json('GET', route('posts.read', ['post' => 123]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'Post Not Found',
                ],
            ],
        ]);
    }

    /**
     * @group posts::read
     * @test
     */
    public function itFailsToReadUnpublishedPostFromAnotherAuthor(): void
    {
        $post = factory(Post::class)->create([
            'published_at' => null,
        ]);

        $response = $this->json('GET', route('posts.read', ['post' => $post->id]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'This action is unauthorized.',
                ],
            ],
        ]);
    }

    /**
     * @group posts::read
     * @test
     */
    public function itSuccessfullyReadsPublishedPostFromAnotherAuthor(): void
    {
        $post = factory(Post::class)->create([
            'published_at' => Carbon::now(),
        ]);

        $response = $this->json('GET', route('posts.read', ['post' => $post->id]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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

    /**
     * @group posts::read
     * @test
     */
    public function itSuccessfullyReadsOwnUnpublishedPost(): void
    {
        $post = factory(Post::class)->create([
            'author_id'    => $this->getApiUser()->id,
            'published_at' => null,
        ]);

        $response = $this->json('GET', route('posts.read', ['post' => $post->id]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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

    /**
     * @group posts::read
     * @test
     */
    public function itSuccessfullyReadsOwnPublishedPost(): void
    {
        $post = factory(Post::class)->create([
            'author_id'    => $this->getApiUser()->id,
            'published_at' => Carbon::now(),
        ]);

        $response = $this->json('GET', route('posts.read', ['post' => $post->id]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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

    /**
     * @group posts::create
     * @test
     */
    public function itFailsToCreatePostDueToValidationErrors(): void
    {
        $response = $this->json('POST', route('posts.create'), [
            'title'        => str_repeat('foo', 100),
            'published_at' => 123,
        ], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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
        $response = $this->json('POST', route('posts.create'), [
            'title'        => 'Article Title',
            'content'      => 'Article content.',
            'published_at' => '2000-01-02 00:11:22',
        ], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(201);
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

    /**
     * @group posts::update
     * @test
     */
    public function itFailsToUpdatePostDueToNotFoundError(): void
    {
        $response = $this->json('PUT', route('posts.update', ['post' => 123]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'Post Not Found',
                ],
            ],
        ]);
    }

    /**
     * @group posts::update
     * @test
     */
    public function itFailsToUpdatePostDueToAuthorisationError(): void
    {
        $post = factory(Post::class)->create();

        $response = $this->json('PUT', route('posts.update', ['post' => $post->id]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'This action is unauthorized.',
                ],
            ],
        ]);
    }

    /**
     * @group posts::update
     * @test
     */
    public function itFailsToUpdatePostDueToValidationErrors(): void
    {
        $post = factory(Post::class)->create([
            'author_id' => $this->getApiUser()->id,
        ]);

        $response = $this->json('PUT', route('posts.update', ['post' => $post->id]), [
            'title'        => str_repeat('foo', 100),
            'content'      => false,
            'published_at' => 123,
        ], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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
                    'detail' => 'The content must be a string.',
                ],
                [
                    'id'     => 'published_at',
                    'detail' => 'The published at is not a valid date.',
                ],
            ],
        ]);
    }

    /**
     * @group posts::update
     * @test
     */
    public function itSuccessfullyUpdatesPost(): void
    {
        $post = factory(Post::class)->create([
            'author_id' => $this->getApiUser()->id,
        ]);

        $response = $this->json('PUT', route('posts.update', ['post' => $post->id]), [
            'title'        => 'Article Title',
            'content'      => 'Article content.',
            'published_at' => '2000-01-02 00:11:22',
        ], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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

    /**
     * @group posts::delete
     * @test
     */
    public function itFailsToDeletePostDueToNotFoundError(): void
    {
        $response = $this->json('DELETE', route('posts.delete', ['post' => 123]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'Post Not Found',
                ],
            ],
        ]);
    }

    /**
     * @group posts::delete
     * @test
     */
    public function itFailsToDeletePostDueToAuthorisationError(): void
    {
        $post = factory(Post::class)->create();

        $response = $this->json('DELETE', route('posts.delete', ['post' => $post->id]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'This action is unauthorized.',
                ],
            ],
        ]);
    }

    /**
     * @group posts::update
     * @test
     */
    public function itSuccessfullyDeletesPost(): void
    {
        $post = factory(Post::class)->create([
            'author_id' => $this->getApiUser()->id,
        ]);

        $response = $this->json('DELETE', route('posts.delete', ['post' => $post->id]), [], [
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
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
