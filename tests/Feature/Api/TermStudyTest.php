<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\TermStudy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TermStudyTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'term-studies.';

    public function testCanGetAllTermStudy(): void
    {
        TermStudy::factory()->count(3)->create();

        $response = $this->getJson(route("{$this->route}index"));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'title',
                    'year',
                    'month',
                    'course',
                    'module'
                ]
            ]
        ]);
    }

    public function testCanStoreTermStudy(): void
    {
        $termStudy = TermStudy::factory()->make();

        $response = $this->postJson(route("{$this->route}store", $termStudy->toArray()));

        $response->assertCreated()
            ->assertJson(['message'=> __('messages.Created')]);

        $this->assertDatabaseHas('term_studies', $termStudy->toArray());
    }

    public function testCanUpdateTermStudy(): void
    {
        $existTermStudy = TermStudy::factory()->create();
        $termStudy = TermStudy::factory()->make();

        $response = $this->putJson(route("{$this->route}update", $existTermStudy->id), $termStudy->toArray());
        
        $response->assertStatus(202)
            ->assertJson(['message' => __('messages.Updated')]);

        $this->assertDatabaseHas('term_studies', $termStudy->toArray());
    }

    public function testCanShowTermStudy(): void
    {
        $termStudy = TermStudy::factory()->create();

        $response = $this->getJson(route("{$this->route}show", $termStudy->id));

        $singularOrPluralYearWord = $termStudy->year == 1 ? 'рік' : 'роки'; 
        $singularOrPluralMonthWord = $termStudy->month == 1 ? 'місяць' : 'місяців';

        $description =
            "{$termStudy->year} {$singularOrPluralYearWord} {$termStudy->month} {$singularOrPluralMonthWord} ({$termStudy->title})";
        
        $response->assertOk()->assertExactJson([
            'data' => [
                'title' => $description,
                'year' => $termStudy->year,
                'month' => $termStudy->month,
                'course' => $termStudy->course,
                'module' => $termStudy->module,                
            ]
        ]);
    }

    public function testCanDeleteTermStudy(): void
    {
        $termStudy = TermStudy::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $termStudy));

        $response->assertStatus(200)
            ->assertJson(['message' => __('messages.Deleted')]);
        
        $this->assertDatabaseMissing('term_studies', $termStudy->toArray());
    }

    public function testCanGetTermStudyList(): void
    {
        TermStudy::factory()->count(3)->create();

        $response = $this->getJson(route("{$this->route}select"));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                ]
            ]
        ]);
    }
}
