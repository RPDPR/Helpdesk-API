<?php

namespace Tests\Feature\Api\V1\Ticket;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StatusChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(['accept' => 'application/json']);
    }

    #[Test]
    public function test_regular_user_cant_change_ticket_status(): void
    {
        $user = User::factory()->create(['role' => UserRole::user->value]);
        $ticket = Ticket::factory()->create(['status' => TicketStatus::open->value]);

        $response = $this->actingAs($user)->post("/api/v1/tickets/{$ticket->id}/status", [
            'status' => TicketStatus::closed->value,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('ticket_events', 0);
    }

    #[Test]
    public function test_agent_can_change_ticket_status_and_it_creates_a_new_event(): void
    {
        $this->withoutExceptionHandling();

        $agent = User::factory()->create(['role' => UserRole::agent->value]);
        $ticket = Ticket::factory()->create(['status' => TicketStatus::open->value]);

        $response = $this->actingAs($agent)->post("/api/v1/tickets/{$ticket->id}/status", [
            'status' => TicketStatus::closed->value,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('ticket_events', 1);
    }

    #[Test]
    public function test_admin_can_change_ticket_status_and_it_creates_a_new_event(): void
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create(['role' => UserRole::admin->value]);
        $ticket = Ticket::factory()->create(['status' => TicketStatus::open->value]);

        $response = $this->actingAs($admin)->post("/api/v1/tickets/{$ticket->id}/status", [
            'status' => TicketStatus::closed->value,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('ticket_events', 1);
    }
}
