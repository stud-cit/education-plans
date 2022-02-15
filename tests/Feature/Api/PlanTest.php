<?php

namespace Tests\Feature\Api;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function testCanDeletePlan()
    {
        $plan = Plan::factory()->create();

        $response = $this->deleteJson(route('plans.destroy', $plan));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('plans', $plan->toArray());
    }

    public function testCanGetAllPlans()
    {
        $plan = Plan::factory()->create();
        $response = $this->getJson('/api/plans');

        $response->assertOk();

        $response->assertJson([
            'data' => [
                [
                    'id' => $plan->id,
                    'title' => $plan->title,
                    'faculty' => $plan->faculty,
                    'department' => $plan->department,
                    'year' => $plan->year,
                    'created_at' => $plan->created_at,
                ]
            ]
        ]);

    }
}
