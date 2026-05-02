<?php

namespace Tests\Feature\Api;

use App\Models\Note;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'notes.';
    private $table = 'notes';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
        $this->seed([RoleSeeder::class]);
    }

    public function testCanGetAllNotes(): void
    {
        $this->actingAsUser();

        Note::factory()->count(10)->create();

        $response = $this->getJson(route("{$this->route}index"));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => ['id', 'abbreviation', 'explanation']
            ]
        ]);
    }

    public function testCanStoreNote(): void
    {
        $user = User::factory()->admin()->create();

        $position = Note::factory()->make();

        $response = $this->actingAs($user)->postJson(route("{$this->route}store"), $position->toArray());

        $response->assertCreated()->assertJson(['message' => __('messages.Created')]);
        $this->assertDatabaseHas($this->table, $position->toArray());
    }

    public function testCanUpdateNote(): void
    {
        $user = User::factory()->admin()->create();

        $oldNote = Note::factory()->create();
        $newNote = Note::factory()->make();

        $response = $this->actingAs($user)->putJson(
            route("{$this->route}update", $oldNote->id),
            [
                'abbreviation' => $newNote->abbreviation,
                'explanation' =>  $newNote->explanation
            ]
        );

        $response->assertCreated()
            ->assertJson(['message' => __('messages.Updated')]);

        $this->assertDatabaseHas($this->table, $newNote->toArray());
    }

    public function testCanDeleteNote(): void
    {
        $user = User::factory()->admin()->create();

        $note = Note::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route("{$this->route}destroy", $note->id));

        $response->assertOk()->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing($this->table, $note->toArray());
    }

    public function testGetRules(): void
    {
        $this->actingAsUser();
        Note::factory()->count(2)->create();

        $response = $this->getJson(route("{$this->route}rules", ['year' => '2024']));

        $response->assertOk()->assertJsonStructure([
            'data' => [
                'rule',
                'notes'
            ]
        ]);

        $this->assertTrue(str_ends_with($response['data']['notes'], '.'));
    }
}
