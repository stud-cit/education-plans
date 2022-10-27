<?php

namespace App\Helpers\Filters\CatalogSelectiveSubjectFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Department extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('department', function ($query) use ($value) {
            return $query->where('department_id', $value);
        });
    }
}
