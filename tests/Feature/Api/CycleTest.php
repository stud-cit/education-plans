<?php

namespace Tests\Feature\Api;

use App\Models\Cycle;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CycleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
        $this->seed([RoleSeeder::class]);
    }

    public function testCanStoreCycle()
    {
        $user = User::factory()->admin()->create();
        $cycle = Cycle::factory()->make();

        $this->actingAs($user);
        $response = $this->postJson(route("cycles.store", $cycle->toArray()));

        $response->assertStatus(201)->assertJsonStructure([
            'data' => [
                'id',
                'credit',
                'list_cycle_id',
                'cycle_id'
            ]
        ]);
    }
}
