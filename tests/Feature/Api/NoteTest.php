<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Note;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'notes.';
    private $table = 'notes';

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
        $this->actingAsUser();

        $position = Note::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $position->toArray());

        $response->assertCreated()->assertJson(['message' => __('messages.Created')]);
        $this->assertDatabaseHas($this->table, $position->toArray());
    }

    public function testCanUpdateNote(): void
    {
        $this->actingAsUser();

        $oldNote = Note::factory()->create();
        $newNote = Note::factory()->make();

        $response = $this->putJson(
            route("{$this->route}update", $oldNote->id),
            [
                'abbreviation' => $newNote->abbreviation,
                'explanation' =>  $newNote->explanation
            ]
        );

        $response->assertCreated()
            ->assertJson([ 'message' => __('messages.Updated')]);

        $this->assertDatabaseHas($this->table, $newNote->toArray());
    }

    public function testCanDeleteNote(): void
    {
        $this->actingAsUser();

        $note = Note::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $note->id));

        $response->assertOk()->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing($this->table, $note->toArray());
    }

    public function testGetRules(): void
    {
        $this->seed();
        $this->actingAsUser();

        $response = $this->getJson(route("{$this->route}rules"));

        $response->assertOk()->assertJsonStructure([
            'data' => [
                'rule',
                'notes'
            ]
        ]);

        $this->assertTrue(str_ends_with($response['data']['notes'], '.'));
    }
}
