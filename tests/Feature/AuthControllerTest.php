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
    public function itWillNotRegisterDueToValidationErrors(): void
    {
        $response = $this->json('POST', route('auth.register'), [
            'name'     => str_repeat('foo', 100),
            'email'    => 'something else entirely',
            'password' => 123,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 'name',
                    'detail' => 'The name may not be greater than 255 characters.',
                ],
                [
                    'id'     => 'email',
                    'detail' => 'The email must be a valid email address.',
                ],
                [
                    'id'     => 'password',
                    'detail' => 'The password confirmation does not match.',
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
        $response = $this->json('POST', route('auth.register'), [
            'name'                  => 'John Doe',
            'email'                 => 'john.doe@email.com',
            'password'              => 's3cr3t',
            'password_confirmation' => 's3cr3t',
        ], [
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
        $response = $this->json('POST', route('auth.authenticate'), [
            'email' => 'foo',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                [
                    'id'     => 'email',
                    'detail' => 'The email must be a valid email address.',
                ],
                [
                    'id'     => 'password',
                    'detail' => 'The password field is required.',
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
        $response = $this->json('POST', route('auth.authenticate'), [
            'email'    => 'john.doe@email.com',
            'password' => 's3cr3t',
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

        $response = $this->json('POST', route('auth.authenticate'), [
            'email'    => $user->email,
            'password' => 's3cr3t',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }
}
