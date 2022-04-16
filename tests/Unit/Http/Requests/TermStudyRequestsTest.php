<?php

namespace Tests\Unit\Http\Requests;

use App\Models\TermStudy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TermStudyRequestsTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'term-studies.';

    public function testTitleYearMonthCourseModuleIsRequired()
    {
        $validatedTypeFields = ['title', 'year', 'month', 'course', 'module'];
        $brokenRule = null;
        $arrayForValidation = array_fill_keys($validatedTypeFields, $brokenRule);

        $termStudy = TermStudy::factory()->make($arrayForValidation);
        $existsTermStudy = TermStudy::factory()->create();

        $this->postJson(route("{$this->route}store"), $termStudy->toArray())
            ->assertJsonValidationErrors($validatedTypeFields);

        $this->putJson(
            route("{$this->route}update", $existsTermStudy->id),
            $arrayForValidation
        )->assertJsonValidationErrors($validatedTypeFields);
    }

    public function testYearMonthCourseModuleIsNumerics()
    {
        $validatedTypeFields = ['year', 'month', 'course', 'module'];
        $brokenRule = Str::random(40);
        $arrayForValidation = array_fill_keys($validatedTypeFields, $brokenRule);

        $termStudy = TermStudy::factory()->make($arrayForValidation);
        $existsTermStudy = TermStudy::factory()->create();

        $this->postJson(route("{$this->route}store"), $termStudy->toArray())
            ->assertJsonValidationErrors($validatedTypeFields);

        $this->putJson(
            route("{$this->route}update", $existsTermStudy->id),
            $arrayForValidation
        )->assertJsonValidationErrors($validatedTypeFields);
    }
}
