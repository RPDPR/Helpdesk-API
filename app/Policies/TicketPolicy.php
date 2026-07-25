<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return match ($user->role) {
            UserRole::user->value => $user->id === $ticket->user_id,
            UserRole::agent->value => true,
            UserRole::admin->value => true,

            default => false
        };
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return match ($user->role) {
            UserRole::user->value => false,
            UserRole::agent->value => true,
            UserRole::admin->value => true,

            default => false
        };
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return false;
    }

    public function assign(User $user, Ticket $ticket)
    {
        return match ($user->role) {
            UserRole::user->value => false,
            UserRole::agent->value => true,
            UserRole::admin->value => true,

            default => false
        };
    }

    public function changeStatus(User $user, Ticket $ticket)
    {
        return match ($user->role) {
            UserRole::user->value => false,
            UserRole::agent->value => true,
            UserRole::admin->value => true,

            default => false
        };
    }

    public function comment(User $user, Ticket $ticket)
    {
        return match ($user->role) {
            UserRole::user->value => $user->id === $ticket->user_id,
            UserRole::agent->value => true,
            UserRole::admin->value => true,

            default => false
        };
    }
}
