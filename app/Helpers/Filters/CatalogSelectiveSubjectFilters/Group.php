<?php

namespace App\Helpers\Filters\CatalogSelectiveSubjectFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Group extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->whereHas('catalog', function ($query) use ($value) {
            $query->when('group_id', function ($query) use ($value) {
                return $query->where('group_id', $value);
            });
        });
    }
}
