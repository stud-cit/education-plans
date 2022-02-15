<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Models\FormStudy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormStudyRequestTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'form-studies.';

    /**
     * @return void
     */
    public function testTitleIsRequired()
    {
        $validatedTypeField = 'title';
        $brokenRule = null;

        $formStudy = FormStudy::factory()->make([$validatedTypeField => $brokenRule]);

        $this->postJson(
            route($this->route . 'store'), $formStudy->toArray()
        )->assertJsonValidationErrors($validatedTypeField);
    }

    public function testTitleIsNotToLong()
    {
        $faker = \Faker\Factory::create();

        $validatedTypeField = 'title';
        $brockenRule = $faker->paragraph(20);

        $formStudy = FormStudy::factory()->make([$validatedTypeField => $brockenRule]);

        $this->postJson(
            route($this->route . 'store'),
            $formStudy->toArray()
        )->assertJsonValidationErrors($validatedTypeField);
    }
}
