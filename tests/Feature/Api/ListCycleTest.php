<?php

namespace Tests\Feature\Api;

use App\Models\ListCycle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCycleTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'list-cycles.';

    public function testGelAllListCycles(): void
    {
        $this->actingAsUser();

        ListCycle::factory()->count(10)->create();

        $response = $this->getJson(route("{$this->route}index"));

        $response->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title'
                ]
            ]
        ]);
    }
}
