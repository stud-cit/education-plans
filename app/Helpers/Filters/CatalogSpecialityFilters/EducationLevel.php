<?php

namespace App\Helpers\Filters\CatalogSpecialityFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class EducationLevel extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('educationLevel', function ($query) use ($value) {
            return $query->where('catalog_education_level_id', $value);
        });
    }
}
