<?php

namespace App\Helpers\Filters\PlanFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;


class Id extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('id', function ($query) use ($value) {
            return $query->where('id', $value);
        });
    }
}
