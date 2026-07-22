<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
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
            'accept' => 'application/json'
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
            'password' => 'nikitaPassword'
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token', 'token_type', 'expires_in'
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    #[Test] //
    public function test_attribute_name_is_required_for_registering(): void
    {
        $data =
        [
            'email' => 'nikita@demo',
            'password' => 'nikitaPassword'
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
            'email' => 'nikita@demo',
            'password' => 'nikitaPassword'
        ];

        $response = $this->post('/api/v1/auth/register', $data);

        $response->assertStatus(422);
        $response->assertInvalid('name');

        $this->assertDatabaseCount('users', 0);
    }
}
