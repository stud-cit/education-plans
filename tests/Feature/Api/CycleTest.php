<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Cycle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CycleTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'cycles.';
    private $table = 'cycles';

    public function testCanStoreCycle()
    {
        $this->actingAsUser();

        $cycle = Cycle::factory()->make();

        $response = $this->postJson(route("{$this->route}store", $cycle->toArray()));

        $response->assertStatus(201)->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'credit',
                'cycle_id'
            ]
        ]);
    }
}
