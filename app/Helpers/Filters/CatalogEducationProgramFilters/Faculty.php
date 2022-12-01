<?php

namespace App\Helpers\Filters\CatalogEducationProgramFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Faculty extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('faculty', function ($query) use ($value) {
            return $query->where('faculty_id', $value);
        });
    }
}
