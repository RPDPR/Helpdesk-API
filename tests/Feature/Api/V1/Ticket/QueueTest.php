<?php

namespace Tests\Feature\Api\V1\Ticket;

use App\Models\Ticket;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\TicketPriority;
use App\Jobs\SendTicketEmailJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QueueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders(['accept' => 'application/json']);
    }

    #[Test]
    public function test_ticket_creation_pushes_job_in_queue(): void
    {
        Queue::fake();

        $user = User::factory()->create(['role' => UserRole::user->value]);

        $this->actingAs($user)->post('/api/v1/tickets', [
            'subject' => 'Some subject',
            'body' => 'Some huge body right here',
            'priority' => TicketPriority::high->value
        ]);

        Queue::assertPushed(SendTicketEmailJob::class);
    }
}
