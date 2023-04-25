<?php

namespace App\Helpers\Filters\PlanFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;


class PlanOrTemplate extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('planOrTemplate', function ($query) use ($value) {
            return $query->where('type_id', $value);
        });
    }
}
