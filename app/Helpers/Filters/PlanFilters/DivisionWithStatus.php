<?php

namespace App\Helpers\Filters\PlanFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;
use App\Models\VerificationStatuses;

class DivisionWithStatus extends QueryFilter implements FilterContract
{
    public function handle($values): void
    {
        [$division,  $status] = explode(',', $values);

        switch ($status) {
            case VerificationStatuses::NOT_CHECKED:
                $this->query->whereDoesntHave('verification', function ($query) use ($division) {
                    $query->where('verification_statuses_id', (int) $division);
                });

                break;

            case VerificationStatuses::VERIFIED:
                $this->query->whereHas('verification', function ($query) use ($division) {
                    $query->where('verification_statuses_id', (int) $division)->where('status', true);
                });

                break;

            case VerificationStatuses::NOT_VERIFIED:
                $this->query->whereHas('verification', function ($query) use ($division) {
                    $query->where('verification_statuses_id', (int) $division)->where('status', false);
                });

                break;
        }
    }
}
