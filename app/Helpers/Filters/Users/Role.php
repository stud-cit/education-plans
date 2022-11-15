<?php

namespace App\Helpers\Filters\Users;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;

class Role extends QueryFilter implements FilterContract
{
    public function handle($value): void
    {
        $this->query->when('role', function($query) use ($value) {
            return $query->where('role_id', $value);
        });
    }
}
