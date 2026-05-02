<?php

namespace Tests\Feature\Api;

use App\Models\ListCycle;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCycleTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'list-cycles.';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
        $this->seed([RoleSeeder::class]);
    }

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
