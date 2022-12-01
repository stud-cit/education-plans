<?php

namespace App\Helpers\Filters\CatalogEducationProgramFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class EducationProgram extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('educationProgram', function ($query) use ($value) {
            return $query->where('education_program_id', $value);
        });
    }
}
