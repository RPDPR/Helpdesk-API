<?php

namespace App\Models\Traits;

use App\Http\Filters\FilterInterface;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * @return Builder
     */
    public function scopeFilter(Builder $builder, FilterInterface $filter)
    {
        $filter->apply($builder);

        return $builder;
    }
}
