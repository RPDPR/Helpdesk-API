<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders([
            'accept' => 'application/json',
        ]);
    }

    #[Test] //
    public function test_user_can_be_registered(): void
    {
        $this->withoutExceptionHandling();

        $data =
        [
            'name' => 'Nikita',
            'email' => 'nikita@demo',
            'password' => 'nikitaPassword',
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token', 'token_type', 'expires_in',
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    #[Test] //
    public function test_attribute_name_is_required_for_registering(): void
    {
        $data =
        [
            'email' => 'nikita@demo',
            'password' => 'nikitaPassword',
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(422);
        $response->assertInvalid('name');

        $this->assertDatabaseCount('users', 0);
    }

    #[Test] //
    public function test_attribute_email_is_unique_for_registering(): void
    {
        $data =
        [
            'name' => 'Nikita',
            'email' => 'nikitademo',
            'password' => 'nikitaPassword',
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(422);
        $response->assertInvalid('email');

        $this->assertDatabaseCount('users', 0);
    }

    #[Test] //
    public function test_attribute_password_is_too_small_for_registering(): void
    {
        $data =
        [
            'name' => 'Nikita',
            'email' => 'nikita@demo',
            'password' => 'nikitaP',
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(422);
        $response->assertInvalid('password');

        $this->assertDatabaseCount('users', 0);
    }

    #[Test] //
    public function test_user_can_be_logged_in(): void
    {
        $this->withoutExceptionHandling();

        $data =
        [
            'name' => 'Nikita',
            'email' => 'nikita@demo',
            'password' => 'nikitaPassword',
        ];

        $user = User::factory()->create($data);

        $this->assertDatabaseCount('users', 1);

        unset($data['name']);

        $response = $this->post('/api/v1/auth/login', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token', 'token_type', 'expires_in',
        ]);
    }

    #[Test] //
    public function test_attribute_email_is_required_for_logging(): void
    {
        $data =
        [
            'name' => 'Nikita',
            'email' => 'nikita@demo',
            'password' => 'nikitaPassword',
        ];

        $user = User::factory()->create($data);

        $this->assertDatabaseCount('users', 1);

        unset($data['name']);
        unset($data['email']);

        $response = $this->post('/api/v1/auth/login', $data);

        $response->assertStatus(422);
        $response->assertInvalid('email');
    }

    #[Test] //
    public function test_attribute_password_is_required_for_logging(): void
    {
        $data =
        [
            'name' => 'Nikita',
            'email' => 'nikita@demo',
            'password' => 'nikitaPassword',
        ];

        $user = User::factory()->create($data);

        $this->assertDatabaseCount('users', 1);

        unset($data['name']);
        unset($data['password']);

        $response = $this->post('/api/v1/auth/login', $data);

        $response->assertStatus(422);
        $response->assertInvalid('password');
    }

    #[Test] //
    public function test_unauthorized_user_cant_access_protected_route(): void
    {
        $response = $this->get('/api/v1/tickets');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }
}
