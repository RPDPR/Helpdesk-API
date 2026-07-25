<?php

namespace App\Enums;

enum TicketPriority: string
{
    case low = 'low';
    case normal = 'normal';
    case high = 'high';
}
