<?php

namespace App\Helpers\Filters\CatalogEducationProgramFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class CatalogSubject extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('catalog_subject', function ($query) use ($value) {
            return $query->where('catalog_subject_id', $value);
        });
    }
}
