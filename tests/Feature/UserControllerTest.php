<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use ApiUser;
    use RefreshDatabase;

    /**
     * @group users::list
     * @test
     */
    public function itFailsToGetUsersDueToValidationErrors(): void
    {
        $response = $this->json('GET', route('users.list'), [
            'page'     => 'foo',
            'per_page' => 'bar',
            'search'   => 123,
            'sort'     => 'baz',
            'order'    => 'waz',
        ], [
            'Content-Type'  => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                [
                    'detail' => 'The page must be an integer.',
                    'meta'   => [
                        'field' => 'page',
                    ],
                ],
                [
                    'detail' => 'The per page must be an integer.',
                    'meta'   => [
                        'field' => 'per_page',
                    ],
                ],
                [
                    'detail' => 'The search must be a string.',
                    'meta'   => [
                        'field' => 'search',
                    ],
                ],
                [
                    'detail' => 'The selected sort is invalid.',
                    'meta'   => [
                        'field' => 'sort',
                    ],
                ],
                [
                    'detail' => 'The selected order is invalid.',
                    'meta'   => [
                        'field' => 'order',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @group users::list
     * @test
     */
    public function itSuccessfullyReturnsUsers(): void
    {
        factory(User::class, 20)->create();

        $response = $this->json('GET', route('users.list'), [
            'page'     => 2,
            'per_page' => 5,
            'search'   => 'a',
            'sort'     => 'id',
            'order'    => 'asc',
        ], [
            'Content-Type'  => 'application/vnd.api+json',
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
     * @group users::profile
     * @test
     */
    public function itFailsToReadTheUserProfileDueToMissingApiToken(): void
    {
        $response = $this->json('GET', route('users.profile'), [], [
            'Content-Type' => 'application/vnd.api+json',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'Token not provided',
                ],
            ],
        ]);
    }

    /**
     * @group users::profile
     * @test
     */
    public function itSuccessfullyReadsTheUserProfile(): void
    {
        $response = $this->json('GET', route('users.profile'), [], [
            'Content-Type'  => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * @group users::profile::update
     * @test
     */
    public function itFailsToUpdateProfileDueToMissingApiToken(): void
    {
        $response = $this->json('PUT', route('users.profile.update'), [], [
            'Content-Type' => 'application/vnd.api+json',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'Token not provided',
                ],
            ],
        ]);
    }

    /**
     * @group users::profile::update
     * @test
     */
    public function itFailsToUpdatePostDueToValidationErrors(): void
    {
        $response = $this->json('PUT', route('users.profile.update'), [
            'name'     => str_repeat('foo', 100),
            'email'    => str_repeat('bad email', 100),
            'password' => 123,
        ], [
            'Content-Type'  => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                [
                    'detail' => 'The name may not be greater than 255 characters.',
                    'meta'   => [
                        'field' => 'name',
                    ],
                ],
                [
                    'detail' => 'The email must be a valid email address.',
                    'meta'   => [
                        'field' => 'email',
                    ],
                ],
                [
                    'detail' => 'The email may not be greater than 255 characters.',
                    'meta'   => [
                        'field' => 'email',
                    ],
                ],
                [
                    'detail' => 'The password confirmation does not match.',
                    'meta'   => [
                        'field' => 'password',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @group users::profile::update
     * @test
     */
    public function itSuccessfullyUpdatesPost(): void
    {
        $response = $this->json('PUT', route('users.profile.update'), [
            'name'                  => 'John Doe',
            'email'                 => 'john.doe@email.com',
            'password'              => 's3cr3t',
            'password_confirmation' => 's3cr3t',
        ], [
            'Content-Type'  => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
