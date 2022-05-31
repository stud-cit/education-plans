<?php

namespace Tests\Unit\Http\Requests;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanRequestTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'plans.';
}
