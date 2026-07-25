<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;

class TicketFilter extends AbstractFilter
{
    public const SUBJECT = 'subject';

    public const BODY = 'body';

    public const STATUS = 'status';

    public const PRIORITY = 'priority';

    public const ASSIGNED_AGENT_ID = 'assigned_agent_id';

    public function subject(Builder $builder, $value)
    {
        $builder->where('subject', 'like', "%{$value}%");
    }

    public function body(Builder $builder, $value)
    {
        $builder->where('body', 'like', "%{$value}%");
    }

    public function status(Builder $builder, $value)
    {
        $builder->where('status', $value);
    }

    public function priority(Builder $builder, $value)
    {
        $builder->where('priority', $value);
    }

    public function assigned_agent_id(Builder $builder, $value)
    {
        $builder->where('assigned_agent_id', $value);
    }

    protected function getCallbacks(): array
    {
        $callbackArray =
        [
            self::SUBJECT => [$this, 'subject'],
            self::BODY => [$this, 'body'],
            self::STATUS => [$this, 'status'],
            self::PRIORITY => [$this, 'priority'],
            self::ASSIGNED_AGENT_ID => [$this, 'assigned_agent_id'],
        ];

        return $callbackArray;
    }
}
