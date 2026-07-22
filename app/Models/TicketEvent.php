<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['ticket_id', 'type', 'payload', 'author_id'])]
class TicketEvent extends Model
{
    public function ticket(){
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }
}
