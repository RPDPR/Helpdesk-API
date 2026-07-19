<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\Ticket\FilterRequest;
use App\Http\Filters\TicketFilter;
use App\Http\Requests\Api\V1\Ticket\StoreRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index(FilterRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        if(isset($data['assigned']))
        {
            if($data['assigned'] === 'me')
            {
                unset($data['assigned']);
                $data['assigned_agent_id'] = $user->id;
            }
            else if ($data['assigned'] === 'unassigned')
            {
                unset($data['assigned']);
                $data['assigned_agent_id'] = null;
            }
        }

        $filter = app()->make(TicketFilter::class, ['queryParams' => array_filter($data)]);
        $tickets = match ($user->role)
        {
            UserRole::user->value => $user->tickets()->filter($filter)->paginate(10)->OnEachSide(1),
            UserRole::agent->value => Ticket::filter($filter)->paginate(10)->OnEachSide(1),
            UserRole::admin->value => Ticket::filter($filter)->paginate(10)->OnEachSide(1),

            default => $user->tickets()->filter($filter)->paginate(10)->OnEachSide(1)
        };

        return TicketResource::collection($tickets)->resolve();
    }

    public function store(StoreRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        $data['user_id'] = $user->id;
        $data['status'] = TicketStatus::open->value;

        $ticket = Ticket::firstOrCreate($data);

        return TicketResource::make($ticket)->resolve();
    }

    public function show($id)
    {
        $user = auth()->user();

        $ticket = match($user)
        {
            UserRole::user->value => $user->tickets
        };

        return TicketResource::make($ticket)->resolve();
    }

    public function edit(Ticket $ticket)
    {
        //
    }

    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    public function destroy(Ticket $ticket)
    {
        //
    }
}
