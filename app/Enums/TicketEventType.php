<?php

namespace App\Enums;

enum TicketEventType: string
{
    case created = 'created';
    case assigned = 'assigned';
    case status_changed = 'status_changed';
    case comment = 'comment';
}
