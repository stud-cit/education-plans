<?php

namespace App\Helpers\Filters\CatalogSubjectFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Group extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('group_id', function ($query) use ($value) {
            return $query->where('group_id', $value);
        });
    }
}
