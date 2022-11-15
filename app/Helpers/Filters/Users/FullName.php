<?php

namespace App\Helpers\Filters\Users;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class FullName extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('fullName', function($query) use ($value) {
            return $query->where('name', 'like', "%$value%");
        });
    }
}
