<?php

namespace Tests\Feature\Api;

use App\Models\Signature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignatureTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'signatures.';
    private $table = 'signatures';

    public function testCanStoreSignature()
    {
        $this->actingAsUser();

        $signature = Signature::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $signature->toArray());

        $response->assertCreated()
            ->assertJson(['message' => __('messages.Created')]);

        $this->assertDatabaseHas($this->table, $signature->toArray());
    }

    public function testCanUpdateSignature()
    {
        $this->actingAsUser();

        $oldSignature = Signature::factory()->create();
        $signature = Signature::factory()->make();

        $response = $this->putJson(route("{$this->route}update", $oldSignature->id), $signature->toArray());

        $response->assertCreated()
            ->assertJson(['message' => __('messages.Updated')]);

        $this->assertDatabaseHas($this->table, $signature->toArray());
    }

    public function testCanDeleteSignature()
    {
        $this->actingAsUser();

        $signature = Signature::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $signature->id));

        $response->assertStatus(200)
            ->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing($this->table, $signature->toArray());
    }
}
