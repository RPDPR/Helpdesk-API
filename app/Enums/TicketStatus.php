<?php

namespace App\Enums;

enum TicketStatus: string
{
    case open = 'open';
    case in_progress = 'in_progress';
    case resolved = 'resolved';
    case closed = 'closed';
}
