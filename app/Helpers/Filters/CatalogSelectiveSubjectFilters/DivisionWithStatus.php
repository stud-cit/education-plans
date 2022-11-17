<?php

namespace App\Helpers\Filters\CatalogSelectiveSubjectFilters;

use App\Helpers\Filters\QueryFilter;
use App\Helpers\Filters\FilterContract;
use App\Models\VerificationStatuses;

class DivisionWithStatus extends QueryFilter implements FilterContract
{
    public function handle($values): void
    {
        $modelVerificationStatuses = new VerificationStatuses;
        $values = explode(',', $values);
        $division = $values[0];
        $status = $values[1];

        switch ($status) {
            case $modelVerificationStatuses::NOT_CHECKED:
                $this->query->whereDoesntHave('verifications', function ($query) use ($division) {
                    $query->where('verification_status_id', (int) $division);
                });

                break;

            case $modelVerificationStatuses::VERIFIED:
                $this->query->whereHas('verifications', function ($query) use ($division) {
                    $query->where('verification_status_id', (int) $division)
                        ->where('status', true);
                });

                break;

            case $modelVerificationStatuses::NOT_VERIFIED:
               $this->query->whereHas('verifications', function ($query) use ($division) {
                    $query->where('verification_status_id', (int) $division)
                        ->where('status', false);
                });

                break;
        }
    }
}
