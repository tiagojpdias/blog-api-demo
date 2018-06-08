<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use ApiUser;
    use RefreshDatabase;

    /**
     * @group auth::register
     * @test
     */
    public function itWillNotRegisterDueToInvalidContentType(): void
    {
        $response = $this->json('POST', route('auth::register'), [], [
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(412);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'Invalid Content-Type header',
                ],
            ],
        ]);
    }

    /**
     * @group auth::register
     * @test
     */
    public function itWillNotRegisterDueToValidationErrors(): void
    {
        $response = $this->json('POST', route('auth::register'), [
            'name'     => str_repeat('foo', 100),
            'email'    => str_repeat('bad email', 100),
            'password' => 123,
        ], [
            'Content-Type' => 'application/vnd.api+json',
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
     * @group auth::register
     * @test
     */
    public function itWillSuccessfullyRegister(): void
    {
        $response = $this->json('POST', route('auth::register'), [
            'name'                  => 'John Doe',
            'email'                 => 'john.doe@email.com',
            'password'              => 's3cr3t',
            'password_confirmation' => 's3cr3t',
        ], [
            'Content-Type'  => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(201);
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
     * @group auth::authenticate
     * @test
     */
    public function itWillNotAuthenticateDueToValidationErrors(): void
    {
        $response = $this->json('POST', route('auth::authenticate'), [
            'email' => str_repeat('bad email', 100),
        ], [
            'Content-Type' => 'application/vnd.api+json',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                [
                    'detail' => 'The email must be a valid email address.',
                    'meta'   => [
                        'field' => 'email',
                    ],
                ],
                [
                    'detail' => 'The password field is required.',
                    'meta'   => [
                        'field' => 'password',
                    ],
                ],
            ],
        ]);
    }

    /**
     * @group auth::authenticate
     * @test
     */
    public function itWillNotAuthenticateDueToInvalidCredentials(): void
    {
        $response = $this->json('POST', route('auth::authenticate'), [
            'email'    => 'john.doe@email.com',
            'password' => 's3cr3t',
        ], [
            'Content-Type' => 'application/vnd.api+json',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                'title'  => 'Unauthorised',
                'detail' => 'Invalid credentials',
            ],
        ]);
    }

    /**
     * @group auth::authenticate
     * @test
     */
    public function itWillSuccessfullyAuthenticate(): void
    {
        $user = factory(User::class)->create([
            'password' => 's3cr3t',
        ]);

        $response = $this->json('POST', route('auth::authenticate'), [
            'email'    => $user->email,
            'password' => 's3cr3t',
        ], [
            'Content-Type' => 'application/vnd.api+json',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    /**
     * @group auth::invalidate
     * @test
     */
    public function itWillSuccessfullyInvalidate(): void
    {
        $response = $this->json('PUT', route('auth::invalidate'), [], [
            'Content-Type'  => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta' => [
                'info',
            ],
        ]);
    }

    /**
     * @group auth::invalidate
     * @test
     */
    public function itWillSuccessfullyDetectBlacklistedTokenAfterInvalidation(): void
    {
        $token = $this->generateApiUserToken();

        $response = $this->json('PUT', route('auth::invalidate'), [], [
            'Content-Type'  => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $token),
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta' => [
                'info',
            ],
        ]);

        // Perform a second request with the already invalidated token
        $response = $this->json('PUT', route('auth::invalidate'), [], [
            'Content-Type'  => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $token),
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 0,
                    'detail' => 'The token has been blacklisted',
                ],
            ],
        ]);
    }

    /**
     * @group auth::refresh
     * @test
     */
    public function itWillSuccessfullyRefresh(): void
    {
        $response = $this->json('POST', route('auth::refresh'), [], [
            'Content-Type'  => 'application/vnd.api+json',
            'Authorization' => sprintf('Bearer %s', $this->generateApiUserToken()),
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }
}
