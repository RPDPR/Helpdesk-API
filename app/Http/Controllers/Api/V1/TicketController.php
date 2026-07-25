<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Filters\TicketFilter;
use App\Http\Requests\Api\V1\Ticket\ChangeStatusRequest;
use App\Http\Requests\Api\V1\Ticket\CommentRequest;
use App\Http\Requests\Api\V1\Ticket\FilterRequest;
use App\Http\Requests\Api\V1\Ticket\StoreRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Http\Services\Api\V1\TicketService;
use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function __construct(protected TicketService $service) {}

    public function index(FilterRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        if (isset($data['assigned'])) {
            if ($data['assigned'] === 'me') {
                unset($data['assigned']);
                $data['assigned_agent_id'] = $user->id;
            } elseif ($data['assigned'] === 'unassigned') {
                unset($data['assigned']);
                $data['assigned_agent_id'] = null;
            }
        }

        $scope = $user->role === UserRole::user->value ? "user_{$user->id}" : $user->role;
        $filtersHash = md5(json_encode($data));
        $page = $request->page ?? 1;

        $cacheKey = "tickets:list:{$scope}:{$filtersHash}:{$page}";

        $tickets = Cache::tags(['tickets:lists'])->remember($cacheKey, 60, function () use ($user, $data) {
            $filter = app()->make(TicketFilter::class, ['queryParams' => array_filter($data)]);

            return match ($user->role) {
                UserRole::user->value => $user->tickets()->filter($filter)->paginate(10)->OnEachSide(1),
                UserRole::agent->value => Ticket::filter($filter)->paginate(10)->OnEachSide(1),
                UserRole::admin->value => Ticket::filter($filter)->paginate(10)->OnEachSide(1),

                default => $user->tickets()->filter($filter)->paginate(10)->OnEachSide(1)
            };
        });

        return TicketResource::collection($tickets);
    }

    public function store(StoreRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        $ticket = $this->service->create($user, $data);

        return TicketResource::make($ticket)->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $ticket = Cache::remember("ticket:{$id}", 60, function () use ($id) {
            return Ticket::with('events')->findOrFail($id);
        });

        Gate::authorize('view', $ticket);

        return TicketResource::make($ticket);
    }

    public function assign($id)
    {
        $user = auth()->user();

        $ticket = Ticket::findOrFail($id);

        $ticket = $this->service->assign($user, $ticket);

        return TicketResource::make($ticket);
    }

    public function changeStatus($id, ChangeStatusRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        $ticket = Ticket::findOrFail($id);

        $ticket = $this->service->setStatus($user, $ticket, $data);

        return TicketResource::make($ticket);
    }

    public function comment($id, CommentRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        $ticket = Ticket::findOrFail($id);

        $ticket = $this->service->comment($user, $ticket, $data);

        return TicketResource::make($ticket);
    }
}
