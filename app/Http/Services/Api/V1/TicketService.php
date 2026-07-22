<?php

namespace App\Http\Services\Api\V1;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketEvent;
use App\Enums\TicketStatus;
use App\Enums\TicketEventType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendTicketEmailJob;

class TicketService
{
    public function create(User $user, array $data): Ticket|string
    {
        $data['user_id'] = $user->id;
        $data['status'] = TicketStatus::open->value;

        try
        {
            DB::beginTransaction();

            $ticket = Ticket::firstOrCreate($data);

            if($ticket->wasRecentlyCreated)
            {
                $ticketEventData = [
                    'ticket_id' => $ticket->id,
                    'type' => TicketEventType::created->value,
                    'payload' => json_encode(
                        [
                            'subject' => $data['subject'],
                            'priority' => $data['priority'],
                            'author_id' => $user->id,
                        ]
                    )
                ];

                TicketEvent::create($ticketEventData);

                SendTicketEmailJob::dispatch($ticket->id, $ticketEventData['type']);
            }

            $ticket->load('events');

            DB::commit();

            if($ticket->wasRecentlyCreated)
            {
                Cache::tags(['tickets:lists'])->flush();
            }
            Cache::forget("ticket:{$ticket->id}");

            return $ticket;
        }
        catch(\Exception $exception)
        {
            DB::rollBack();

            return $exception->getMessage();
        }
    }

    public function assign(User $user, Ticket $ticket, array $data = []): Ticket|string
    {
        Gate::authorize('assign', $ticket);

        if($ticket->assigned_agent_id === $user->id)
        {
            $ticket->load('events');

            return $ticket;
        }

        try
        {
            DB::beginTransaction();

            $ticket->update([
                'assigned_agent_id' => $user->id
            ]);

            $ticketEventData = [
                'ticket_id' => $ticket->id,
                'type' => TicketEventType::assigned->value,
                'payload' => json_encode(
                    [
                        'assigned_agent_id' => $user->id,
                        'owner_id' => $ticket->user_id,
                        'author_id' => $user->id,
                    ]
                )
            ];

            TicketEvent::create($ticketEventData);

            SendTicketEmailJob::dispatch($ticket->id, $ticketEventData['type']);

            $ticket->load('events');

            DB::commit();
            
            Cache::forget("ticket:{$ticket->id}");
            Cache::tags(['tickets:lists'])->flush();

            return $ticket;
        }
        catch(\Exception $exception)
        {
            DB::rollBack();

            return $exception->getMessage();
        }
    }

    public function setStatus(User $user, Ticket $ticket, array $data): Ticket|string
    {
        Gate::authorize('changeStatus', $ticket);

        if($ticket->status === $data['status'])
        {
            $ticket->load('events');

            return $ticket;
        }

        try
        {
            DB::beginTransaction();

            $ticketOldStatus = $ticket->status;
            $ticket->update($data);

            $ticketEventData = [
                'ticket_id' => $ticket->id,
                'type' => TicketEventType::status_changed->value,
                'payload' => json_encode(
                    [
                        'old_status' => $ticketOldStatus,
                        'new_status' => $data['status'],
                        'author_id' => $user->id,
                    ]
                )
            ];

            TicketEvent::create($ticketEventData);

            SendTicketEmailJob::dispatch($ticket->id, $ticketEventData['type']);

            $ticket->load('events');

            DB::commit();

            Cache::forget("ticket:{$ticket->id}");
            Cache::tags(['tickets:lists'])->flush();

            return $ticket;
        }
        catch(\Exception $exception)
        {
            DB::rollBack();

            return $exception->getMessage();
        }
    }

    public function comment(User $user, Ticket $ticket, array $data): Ticket|string
    {
        Gate::authorize('comment', $ticket);

        try
        {
            DB::beginTransaction();

            $ticket->update($data);

            $ticketEventData = [
                'ticket_id' => $ticket->id,
                'type' => TicketEventType::comment->value,
                'payload' => json_encode(
                    [
                        'text' => $data['text'],
                        'author_id' => $user->id,
                    ]
                )
            ];

            TicketEvent::create($ticketEventData);

            SendTicketEmailJob::dispatch($ticket->id, $ticketEventData['type']);

            $ticket->load('events');

            DB::commit();

            Cache::forget("ticket:{$ticket->id}");
            Cache::tags(['tickets:lists'])->flush();

            return $ticket;
        }
        catch(\Exception $exception)
        {
            DB::rollBack();

            return $exception->getMessage();
        }
    }
}