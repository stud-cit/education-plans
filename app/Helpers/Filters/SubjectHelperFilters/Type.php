<?php

namespace App\Helpers\Filters\SubjectHelperFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Type extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('type', function($query) use ($value) {
            return $query->where('catalog_helper_type_id', $value);
        });
    }
}
