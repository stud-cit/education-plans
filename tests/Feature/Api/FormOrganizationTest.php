<?php

namespace Tests\Feature\Api;

use App\Models\FormOrganization;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormOrganizationTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'form-organizations.';
    private $table = 'form_organizations';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
        $this->seed([RoleSeeder::class]);
    }

    /**
     *
     * @return void
     */
    public function testCanGetAllFormOrganization()
    {
        $this->actingAsUser();

        FormOrganization::factory()->count(3)->create();

        $response = $this->getJson(route("{$this->route}index"));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title'
                ]
            ]
        ]);
    }

    public function testCanStoreFormOrganization()
    {
        $user = User::factory()->admin()->create();

        $newFormOrganization = FormOrganization::factory()->make();

        $response = $this->actingAs($user)->postJson(route("{$this->route}store"), $newFormOrganization->toArray());

        $response->assertCreated();

        $response->assertJson(['message' => __('messages.Created')]);

        $this->assertDatabaseHas($this->table, $newFormOrganization->toArray());
    }

    public function testCanUpdateFormOrganization(): void
    {
        $user = User::factory()->admin()->create();

        $formOrganization = FormOrganization::factory()->create();
        $newFormOrganization = FormOrganization::factory()->make();

        $response = $this->actingAs($user)->putJson(
            route("{$this->route}update", $formOrganization),
            ['title' => $newFormOrganization->title]
        );

        $response->assertStatus(200)->assertJson(['message' => __('messages.Updated')]);

        $this->assertDatabaseHas($this->table, $newFormOrganization->toArray());
    }

    public function testCanDeleteFormOrganization(): void
    {
        $user = User::factory()->admin()->create();

        $formOrganization = FormOrganization::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route("{$this->route}destroy", $formOrganization));

        $response->assertStatus(200)->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing($this->table, $formOrganization->toArray());
    }
}
