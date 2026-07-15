<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketEvent extends Model
{
    public function ticket(){
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }
}
