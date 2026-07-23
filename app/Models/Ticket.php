<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\Filterable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Visible;

#[Fillable(['user_id', 'subject', 'body', 'status', 'priority', 'assigned_agent_id'])]
class Ticket extends Model
{
    use HasFactory, Filterable;
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function events(){
        return $this->hasMany(TicketEvent::class)->orderByDesc('id');
    }
}
