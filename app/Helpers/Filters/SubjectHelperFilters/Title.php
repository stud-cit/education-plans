<?php

namespace App\Helpers\Filters\SubjectHelperFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Title extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('title', function($query) use ($value) {
            return $query->where('title', 'like', "%$value%");
        });
    }
}
