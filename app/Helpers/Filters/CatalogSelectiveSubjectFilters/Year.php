<?php

namespace App\Helpers\Filters\CatalogSelectiveSubjectFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Year extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        clock("year:", $this->query);
        $this->query->whereHas('selectiveCatalog', function ($query) use ($value) {
            $query->when('year', function ($query) use ($value) {
                return $query->where('year', $value);
            });
        });
    }
}
