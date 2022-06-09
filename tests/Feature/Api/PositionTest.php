<?php

namespace Tests\Feature\Api;

use App\Models\Position;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'positions.';
    private $table = 'positions';

    public function testCanGetAllPositions(): void
    {
        $this->actingAsUser();

        Position::factory()->count(10)->create();

        $response = $this->getJson(route("{$this->route}index"));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => ['id', 'position']
            ]
        ]);
    }

    public function testCanStorePosition(): void
    {
        $this->actingAsUser();

        $position = Position::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $position->toArray());

        $response->assertCreated()->assertJson(['message' => __('messages.Created')]);
        $this->assertDatabaseHas($this->table, $position->toArray());
    }

    public function testCanUpdatePosition(): void
    {
        $this->actingAsUser();

        $oldPosition = Position::factory()->create();
        $newPosition = Position::factory()->make();

        $response = $this->patchJson(
            route("{$this->route}update", $oldPosition->id),
            ['position' => $newPosition->position]
        );

        $response->assertCreated()
            ->assertJson([ 'message' => __('messages.Updated')]);

        $this->assertDatabaseHas($this->table, $newPosition->toArray());
    }

    public function testCanDeletePosition(): void
    {
        $this->actingAsUser();

        $position = Position::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $position->id));

        $response->assertOk()->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing($this->table, $position->toArray());
    }

    private function actingAsUser()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
    }
}
