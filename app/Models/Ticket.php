<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'subject', 'body', 'status', 'priority', 'assigned_agent_id'])]
class Ticket extends Model
{
    use Filterable, HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function events()
    {
        return $this->hasMany(TicketEvent::class)->orderByDesc('id');
    }
}
