<?php

namespace Tests\Feature\Api\V1\Ticket;

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(['accept' => 'application/json']);
    }

    #[Test]
    public function test_regular_user_cant_assign_ticket(): void
    {
        $user = User::factory()->create(['role' => UserRole::user->value]);
        $ticket = Ticket::factory()->create(['assigned_agent_id' => null]);

        $response = $this->actingAs($user)->post("/api/v1/tickets/{$ticket->id}/assign");

        $response->assertStatus(403);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_agent_id' => null,
        ]);
        $this->assertDatabaseCount('ticket_events', 0);
    }

    #[Test]
    public function test_agent_can_assign_ticket_and_it_creates_a_new_event(): void
    {
        $this->withoutExceptionHandling();

        $agent = User::factory()->create(['role' => UserRole::agent->value]);
        $ticket = Ticket::factory()->create(['assigned_agent_id' => null]);

        $response = $this->actingAs($agent)->post("/api/v1/tickets/{$ticket->id}/assign");

        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_agent_id' => $agent->id,
        ]);
        $this->assertDatabaseCount('ticket_events', 1);
    }

    #[Test]
    public function test_admin_can_assign_ticket_and_it_creates_a_new_event(): void
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create(['role' => UserRole::admin->value]);
        $ticket = Ticket::factory()->create(['assigned_agent_id' => null]);

        $response = $this->actingAs($admin)->post("/api/v1/tickets/{$ticket->id}/assign");

        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_agent_id' => $admin->id,
        ]);
        $this->assertDatabaseCount('ticket_events', 1);
    }
}
