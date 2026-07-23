<?php

namespace Tests\Feature\Api\V1\Ticket;

use App\Models\User;
use App\Enums\UserRole;
use App\Enums\TicketPriority;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(['accept' => 'application/json']);
    }

    #[Test]
    public function test_ticket_creation_saves_ticket_and_event(): void
    {
        $this->withoutExceptionHandling();
        
        $user = User::factory()->create(['role' => UserRole::user->value]);
        $data = [
            'subject' => 'Some subject',
            'body' => 'Some huge body right here',
            'priority' => TicketPriority::high->value
        ];

        $response = $this->actingAs($user)->post('/api/v1/tickets', $data);

        $response->assertStatus(201);
        $this->assertDatabaseCount('tickets', 1);
        $this->assertDatabaseCount('ticket_events', 1);
    }
}
