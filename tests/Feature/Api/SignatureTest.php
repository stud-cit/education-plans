<?php

namespace Tests\Feature\Api;

use App\Models\Signature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignatureTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'signatures.';
    private $table = 'signatures';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
    }

    public function testDepartmentCanStoreSignature()
    {
        // todo: improve test by checking possibility what roles can store signature
        $department = User::factory()->department()->create();

        $this->actingAs($department);

        $signature = Signature::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $signature->toArray());

        $response->assertCreated();

        $this->assertDatabaseHas($this->table, $signature->toArray());
    }

    public function testGuestCanNotStoreSignature()
    {
        $guest = User::factory()->guest()->create();

        $this->actingAs($guest);

        $signature = Signature::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $signature->toArray());

        $response->assertForbidden();

        $this->assertDatabaseMissing($this->table, $signature->toArray());
    }

    public function testDepartmentCanUpdateSignature()
    {
        $department = User::factory()->department()->create();

        $this->actingAs($department);

        $oldSignature = Signature::factory()->create();
        $signature = Signature::factory()->make();

        $response = $this->putJson(route("{$this->route}update", $oldSignature->id), $signature->toArray());

        $response->assertCreated();

        $this->assertDatabaseHas($this->table, $signature->toArray());
    }

    public function testDepartmentCanDeleteSignature()
    {
        $department = User::factory()->department()->create();

        $this->actingAs($department);

        $signature = Signature::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $signature->id));

        $response->assertStatus(200)
            ->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing($this->table, $signature->toArray());
    }
}
