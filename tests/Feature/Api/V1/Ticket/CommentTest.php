<?php

namespace Tests\Feature\Api\V1\Ticket;

use App\Models\Ticket;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(['accept' => 'application/json']);
    }

    #[Test]
    public function test_regular_user_cant_comment_on_ticket(): void
    {
        $user = User::factory()->create(['role' => UserRole::user->value]);
        $owner = User::factory()->create(['role' => UserRole::user->value]);
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($user)->post("/api/v1/tickets/{$ticket->id}/comment", [
            'text' => 'Some comment'
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('ticket_events', 0);
    }

    #[Test]
    public function test_owner_can_comment_on_his_ticket_and_it_creates_a_new_event(): void
    {
        $this->withoutExceptionHandling();

        $owner = User::factory()->create(['role' => UserRole::user->value]);
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->post("/api/v1/tickets/{$ticket->id}/comment", [
            'text' => 'Some comment'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('ticket_events', 1);
    }

    #[Test]
    public function test_agent_can_comment_on_ticket_and_it_creates_a_new_event(): void
    {
        $this->withoutExceptionHandling();

        $agent = User::factory()->create(['role' => UserRole::agent->value]);
        $owner = User::factory()->create(['role' => UserRole::user->value]);
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($agent)->post("/api/v1/tickets/{$ticket->id}/comment", [
            'text' => 'Some comment'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('ticket_events', 1);
    }

    #[Test]
    public function test_admin_can_comment_on_ticket_and_it_creates_a_new_event(): void
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->create(['role' => UserRole::admin->value]);
        $owner = User::factory()->create(['role' => UserRole::user->value]);
        $ticket = Ticket::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($admin)->post("/api/v1/tickets/{$ticket->id}/comment", [
            'text' => 'Some comment'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount('ticket_events', 1);
    }
}
