<?php

namespace App\Helpers\Filters;

interface FilterContract
{
    public function handle($value): void;
}