<?php

namespace Tests\Feature\Api;

use App\Models\FormStudy;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormStudyTest extends TestCase
{
    use RefreshDatabase;

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
    public function testCanGetAllFormStudies()
    {
        $this->actingAsUser();
        FormStudy::factory()->count(3)->create();

        $response = $this->getJson(route('form-studies.index'));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title'
                ]
            ]
        ]);
    }

    public function testCanStoreFormStudy()
    {
        $user = User::factory()->admin()->create();

        $newFormStudy = FormStudy::factory()->make();

        $response = $this->actingAs($user)->postJson(route('form-studies.store'), $newFormStudy->toArray());

        $response->assertCreated();

        $response->assertJson(['message' => __('messages.Created')]);

        $this->assertDatabaseHas('form_studies', $newFormStudy->toArray());
    }

    public function testCanUpdateFormStudy()
    {
        $user = User::factory()->admin()->create();
        $formStudy = FormStudy::factory()->create();
        $newFormStudy = FormStudy::factory()->make();

        $response = $this->actingAs($user)->putJson(route('form-studies.update', $formStudy), ['title' => $newFormStudy->title]);

        $response->assertStatus(200)->assertJson(['message' => __('messages.Updated')]);

        $this->assertDatabaseHas('form_studies', $newFormStudy->toArray());
    }

    public function testCanDeleteFormStudy(): void
    {
        $user = User::factory()->admin()->create();
        $formStudy = FormStudy::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('form-studies.destroy', $formStudy));

        $response->assertStatus(200)->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing('form_studies', $formStudy->toArray());
    }
}
