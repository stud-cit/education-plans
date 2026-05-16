<?php

namespace Tests\Feature\Api;

use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'positions.';
    private $table = 'positions';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
    }

    public function testCanGetAllPositions(): void
    {
        $guest = User::factory()->guest()->create();
        $this->actingAs($guest);

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
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $position = Position::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $position->toArray());

        $response->assertCreated()->assertJson(['message' => __('messages.Created')]);
        $this->assertDatabaseHas($this->table, $position->toArray());
    }

    public function testGuestCanNotStorePosition(): void
    {
        $guest = User::factory()->guest()->create();
        $this->actingAs($guest);

        $position = Position::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $position->toArray());

        $response->assertForbidden();
        $this->assertDatabaseMissing($this->table, $position->toArray());
    }

    public function testCanUpdatePosition(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $oldPosition = Position::factory()->create();
        $newPosition = Position::factory()->make();

        $response = $this->patchJson(
            route("{$this->route}update", $oldPosition->id),
            ['position' => $newPosition->position]
        );

        $response->assertCreated()
            ->assertJson(['message' => __('messages.Updated')]);

        $this->assertDatabaseHas($this->table, $newPosition->toArray());
    }

    public function testCanDeletePosition(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        $position = Position::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $position->id));

        $response->assertOk()->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing($this->table, $position->toArray());
    }
}
