<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\StudyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudyTermTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'study-terms.';
    private $table = 'study_terms';

    public function testCanGetAllStudyTerm(): void
    {
        $this->actingAsUser();

        StudyTerm::factory()->count(3)->create();

        $response = $this->getJson(route("{$this->route}index"));

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'title',
                    'year',
                    'month',
                    'course',
                    'module',
                    'semesters'
                ]
            ]
        ]);
    }

    public function testCanStoreStudyTerm(): void
    {
        $this->actingAsUser();

        $studyTerm = StudyTerm::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $studyTerm->toArray());

        $response->assertCreated()
            ->assertJson(['message' => __('messages.Created')]);

        $this->assertDatabaseHas($this->table, $studyTerm->toArray());
    }

    public function testCanUpdateTermStudy(): void
    {
        $this->actingAsUser();

        $existStudyTerm = StudyTerm::factory()->create();
        $studyTerm = StudyTerm::factory()->make();

        $response = $this->putJson(route("{$this->route}update", $existStudyTerm->id), $studyTerm->toArray());

        $response->assertStatus(200)
            ->assertJson(['message' => __('messages.Updated')]);

        $this->assertDatabaseHas($this->table, $studyTerm->toArray());
    }

    public function testCanShowTermStudy(): void
    {
        $this->actingAsUser();

        $studyTerm = StudyTerm::factory()->create();

        $response = $this->getJson(route("{$this->route}show", $studyTerm->id));

        $response->assertOk()->assertExactJson([
            'data' => [
                'id' => $studyTerm->id,
                'title' => $studyTerm->description,
                'year' => $studyTerm->year,
                'month' => $studyTerm->month,
                'course' => $studyTerm->course,
                'module' => $studyTerm->module,
                'semesters' => $studyTerm->semesters,
            ]
        ]);
    }

    public function testCanDeleteStudyTerm(): void
    {
        $this->actingAsUser();

        $studyTerm = StudyTerm::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $studyTerm));

        $response->assertStatus(200)
            ->assertJson(['message' => __('messages.Deleted')]);

        $this->assertDatabaseMissing($this->table, $studyTerm->toArray());
    }

    public function testCanGetTermStudyList(): void
    {
        $this->actingAsUser();

        StudyTerm::factory()->count(3)->create();

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
