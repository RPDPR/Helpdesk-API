<?php

namespace App\Models;

use App\Models\Traits\Filterable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'subject', 'body', 'status', 'priority'])]
class Ticket extends Model
{
    use Filterable;
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ticketEvents(){
        return $this->hasMany(TicketEvent::class);
    }
}
