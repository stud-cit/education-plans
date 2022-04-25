<?php

namespace Tests\Feature\Api;

use App\Models\FormStudy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormStudyTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testCanGetAllFormStudies()
    {
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
        $newFormStudy = FormStudy::factory()->make();

        $response = $this->postJson(route('form-studies.store'), $newFormStudy->toArray());

        $response->assertCreated();

        $response->assertJson(['message'=> __('messages.Created')]);

        $this->assertDatabaseHas('form_studies', $newFormStudy->toArray());
    }

    public function testCanUpdateFormStudy()
    {
        $formStudy = FormStudy::factory()->create();
        $newFormStudy = FormStudy::factory()->make();

        $response = $this->putJson(
            route('form-studies.update', $formStudy),
            ['title' => $newFormStudy->title]
        );

        $response->assertStatus(202)->assertJson(['message'=> __('messages.Updated')]);

        $this->assertDatabaseHas('form_studies', $newFormStudy->toArray());
    }

    public function testCanDeleteFormStudy(): void
    {
        $formStudy = FormStudy::factory()->create();

        $response = $this->deleteJson(route('form-studies.destroy', $formStudy));

        $response->assertStatus(200)->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing('form_studies', $formStudy->toArray());
    }
}
