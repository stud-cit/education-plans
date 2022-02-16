<?php

namespace App\Helpers\Filters\PlanFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Title extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->where('title', 'like', "%$value%");
    }
}