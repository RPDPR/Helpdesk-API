<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ticket_id', 'type', 'payload'])]
class TicketEvent extends Model
{
    protected $casts = ['payload' => 'array'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }
}
