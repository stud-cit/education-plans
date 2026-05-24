<?php

namespace Tests\Feature\Api;

use App\Http\Resources\CycleShowResource;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
        $this->seed([RoleSeeder::class]);
    }

    public function testCanDeletePlan()
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $plan = Plan::factory()->create();

        $response = $this->deleteJson(route('plans.destroy', $plan));

        $response->assertStatus(204);
    }

    public function testCanGetAllPlans()
    {
        $this->actingAsUser();

        $plan = Plan::factory()->create();

        $response = $this->getJson(route('plans.index'));

        $response->assertOk();
    }

    public function testCanShowPlan()
    {
        $this->actingAsUser();

        $plan = Plan::factory()->hasCycles(7)->create();

        $response = $this->getJson(route('plans.show', $plan));

        $response->assertOk();
    }
}
