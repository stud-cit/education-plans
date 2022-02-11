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
        $fromStudy = FormStudy::factory()->create(); 
        
        $response = $this->getJson(route('form-studies.index'));

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'title' => $fromStudy->title,
                ]
            ]
        ]);
    }

    public function testCanStoreFormStudy()
    {
        $newFormStudy = FormStudy::factory()->make();

        $response = $this->postJson(route('form-studies.store'), $newFormStudy->toArray());

        $response->assertCreated();

        $response->assertJson(['message'=> __('Created')]);

        $this->assertDatabaseHas('form_studies', $newFormStudy->toArray());
    }
}
