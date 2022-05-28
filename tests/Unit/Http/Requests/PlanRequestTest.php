<?php

namespace Tests\Unit\Http\Requests;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanRequestTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'plans.';

    public function testsScheduleEducationProcessExists()
    {
        $validatedTypeField = 'schedule_education_process';
        $brokenRule = null;

        $plan = Plan::factory()->create();
        $newPlan = Plan::factory()->make([$validatedTypeField => $brokenRule]);

        $this->patchJson(
            route("{$this->route}schedule-education-process.update", $plan),
            $newPlan->toArray()
        )->assertJsonValidationErrors($validatedTypeField);
    }
}
