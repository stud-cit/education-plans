<?php

namespace Tests\Feature\Api;

use App\Models\FormOrganization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormOrganizationTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'form-organizations.';
    private $table = 'form_organizations';
    /**
     *
     * @return void
     */
    public function testCanGetAllFormOrganization()
    {
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
        $newFormOrganization = FormOrganization::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $newFormOrganization->toArray());

        $response->assertCreated();

        $response->assertJson(['message'=> __('messages.Created')]);

        $this->assertDatabaseHas($this->table, $newFormOrganization->toArray());
    }

    public function testCanUpdateFormOrganization(): void
    {
        $formOrganization = FormOrganization::factory()->create();
        $newFormOrganization = FormOrganization::factory()->make();

        $response = $this->putJson(
            route("{$this->route}update", $formOrganization),
            ['title' => $newFormOrganization->title]
        );

        $response->assertStatus(200)->assertJson(['message'=> __('messages.Updated')]);

        $this->assertDatabaseHas($this->table, $newFormOrganization->toArray());
    }

    public function testCanDeleteFormOrganization(): void
    {
        $formOrganization = FormOrganization::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $formOrganization));

        $response->assertStatus(200)->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing($this->table, $formOrganization->toArray());
    }
}
