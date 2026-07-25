<?php

namespace App\Http\Services\Api\V1;

use App\Enums\TicketEventType;
use App\Enums\TicketStatus;
use App\Jobs\SendTicketEmailJob;
use App\Models\Ticket;
use App\Models\TicketEvent;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TicketService
{
    public function create(User $user, array $data): Ticket
    {
        $data['user_id'] = $user->id;
        $data['status'] = TicketStatus::open->value;

        try {
            DB::beginTransaction();

            $ticket = Ticket::create($data);

            if ($ticket->wasRecentlyCreated) {
                $ticketEventData = [
                    'ticket_id' => $ticket->id,
                    'type' => TicketEventType::created->value,
                    'payload' => [
                        'subject' => $data['subject'],
                        'priority' => $data['priority'],
                        'author_id' => $user->id,
                    ],
                ];

                TicketEvent::create($ticketEventData);
            }

            $ticket->load('events');

            DB::commit();

            if ($ticket->wasRecentlyCreated) {
                SendTicketEmailJob::dispatch($ticket->id, TicketEventType::created->value);

                Cache::tags(['tickets:lists'])->flush();
            }

            return $ticket;
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function assign(User $user, Ticket $ticket, array $data = []): Ticket
    {
        Gate::authorize('assign', $ticket);

        if ($ticket->assigned_agent_id === $user->id) {
            $ticket->load('events');

            return $ticket;
        }

        try {
            DB::beginTransaction();

            $ticket->update([
                'assigned_agent_id' => $user->id,
            ]);

            $ticketEventData = [
                'ticket_id' => $ticket->id,
                'type' => TicketEventType::assigned->value,
                'payload' => [
                    'assigned_agent_id' => $user->id,
                    'owner_id' => $ticket->user_id,
                    'author_id' => $user->id,
                ],
            ];

            TicketEvent::create($ticketEventData);

            $ticket->load('events');

            DB::commit();

            SendTicketEmailJob::dispatch($ticket->id, TicketEventType::assigned->value);

            Cache::tags(['tickets:lists'])->flush();
            Cache::forget("ticket:{$ticket->id}");

            return $ticket;
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function setStatus(User $user, Ticket $ticket, array $data): Ticket
    {
        Gate::authorize('changeStatus', $ticket);

        if ($ticket->status === $data['status']) {
            $ticket->load('events');

            return $ticket;
        }

        try {
            DB::beginTransaction();

            $ticketOldStatus = $ticket->status;
            $ticket->update($data);

            $ticketEventData = [
                'ticket_id' => $ticket->id,
                'type' => TicketEventType::status_changed->value,
                'payload' => [
                    'old_status' => $ticketOldStatus,
                    'new_status' => $data['status'],
                    'author_id' => $user->id,
                ],
            ];

            TicketEvent::create($ticketEventData);

            $ticket->load('events');

            DB::commit();

            SendTicketEmailJob::dispatch($ticket->id, TicketEventType::status_changed->value);

            Cache::tags(['tickets:lists'])->flush();
            Cache::forget("ticket:{$ticket->id}");

            return $ticket;
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    public function comment(User $user, Ticket $ticket, array $data): Ticket
    {
        Gate::authorize('comment', $ticket);

        try {
            DB::beginTransaction();

            $ticketEventData = [
                'ticket_id' => $ticket->id,
                'type' => TicketEventType::comment->value,
                'payload' => [
                    'text' => $data['text'],
                    'author_id' => $user->id,
                ],
            ];

            TicketEvent::create($ticketEventData);

            $ticket->load('events');

            DB::commit();

            SendTicketEmailJob::dispatch($ticket->id, TicketEventType::comment->value);

            Cache::tags(['tickets:lists'])->flush();
            Cache::forget("ticket:{$ticket->id}");

            return $ticket;
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
