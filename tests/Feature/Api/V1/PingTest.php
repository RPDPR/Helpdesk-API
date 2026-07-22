<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PingTest extends TestCase
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
    public function test_ping_succeeded(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/v1/ping');

        $response->assertOk();
    }

    #[Test] //
    public function test_ping_failed(): void
    {
        Route::post('/api/v1/ping', function () {
            abort(500, 'API IS DOWN');
        });

        $response = $this->post('/api/v1/ping');

        $response->assertStatus(500);
    }
}
