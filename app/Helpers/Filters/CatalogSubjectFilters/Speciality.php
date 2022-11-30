<?php

namespace App\Helpers\Filters\CatalogSubjectFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Speciality extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('speciality', function ($query) use ($value) {
            return $query->where('speciality_id', $value);
        });
    }
}
