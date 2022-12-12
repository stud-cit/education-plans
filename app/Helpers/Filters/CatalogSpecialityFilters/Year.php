<?php

namespace App\Helpers\Filters\CatalogSpecialityFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Year extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('year', function ($query) use ($value) {
            return $query->where('year', $value);
        });
    }
}
