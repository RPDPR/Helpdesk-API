<?php

namespace Tests\Feature\Api\V1\Ticket;

use App\Models\User;
use App\Models\Ticket;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(['accept' => 'application/json']);
    }

    #[Test]
    public function test_regular_user_can_only_see_his_own_tickets(): void
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create(['role' => UserRole::user->value]);
        $ownTicket = Ticket::factory()->create(['user_id' => $user->id]);
        $otherTicket = Ticket::factory()->create();

        $response = $this->actingAs($user)->get('/api/v1/tickets');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $ownTicket->id]);
        $response->assertJsonMissing(['id' => $otherTicket->id]);
    }

    #[Test]
    public function test_agent_can_see_all_tickets(): void
    {
        $this->withoutExceptionHandling();

        $agent = User::factory()->create(['role' => UserRole::agent->value]);
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();

        $response = $this->actingAs($agent)->get('/api/v1/tickets');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $ticket1->id]);
        $response->assertJsonFragment(['id' => $ticket2->id]);
    }

    #[Test]
    public function test_admin_can_see_all_tickets(): void
    {
        $this->withoutExceptionHandling();
        
        $admin = User::factory()->create(['role' => UserRole::admin->value]);
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();

        $response = $this->actingAs($admin)->get('/api/v1/tickets');

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $ticket1->id]);
        $response->assertJsonFragment(['id' => $ticket2->id]);
    }
}
