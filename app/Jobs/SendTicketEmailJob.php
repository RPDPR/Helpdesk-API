<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class SendTicketEmailJob implements ShouldQueue
{
    use Queueable;

    public int $ticket_id;
    public string $event_type;

    /**
     * Create a new job instance.
     */
    public function __construct(int $ticket_id, string $event_type)
    {
        $this->ticket_id = $ticket_id;
        $this->event_type = $event_type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::table('outbox_emails')->insert([
            'ticket_id'  => $this->ticket_id,
            'event_type' => $this->event_type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
