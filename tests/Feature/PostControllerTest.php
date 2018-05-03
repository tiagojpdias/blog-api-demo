<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function itFailsToGetPublishedPostsDueToValidationErrors(): void
    {
        $response = $this->json('GET', '/posts', [
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
     * @test
     */
    public function itSuccessfullyReturnsPublishedPosts(): void
    {
        factory(Post::class, 20)->create();

        $response = $this->json('GET', '/posts');

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
        ]);
    }
}
