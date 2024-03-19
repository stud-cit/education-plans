<?php

declare(strict_types=1);

namespace App\Traits;

trait TrashedAttribute
{
    public function getIsTrashedAttribute()
    {
        return (bool) $this->deleted_at;
    }
}
