<?php

namespace Tests\Feature\Api;

use App\Models\EducationLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EducationLevelTest extends TestCase
{
    use RefreshDatabase;

    public function testCanGetAllEducationLevels(): void
    {
        $educationLevels = EducationLevel::factory()->create();

        $response = $this->get(route('education-levels.index'));

        $response->assertStatus(200);

        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title'
                ]
            ]
        ]);
    }

    public function testCanStoreEducationLevel(): void
    {
        $educationLevel = EducationLevel::factory()->make();

        $response = $this->postJson(
            route('education-levels.store'),
            $educationLevel->toArray()
        );

        $response->assertCreated();

        $this->assertDatabaseHas('education_levels', $educationLevel->toArray());
    }

    public function testCanShowEducationLevel(): void
    {
        $educationLevel = EducationLevel::factory()->create();

        $response = $this->getJson(
            route('education-levels.show', $educationLevel->id),
        );

        $response->assertok();

        $response->assertJson([
            'data' => [
                'title' => $educationLevel->title
            ]
        ]);
    }

    public function testCanUpdateEducationLevel(): void
    {
        $educationLevel = EducationLevel::factory()->create();
        $newlyEducationLevel = EducationLevel::factory()->make();

        $response = $this->putJson(
            route('education-levels.update', $educationLevel->id),
            ['title' => $newlyEducationLevel->title]
        );

        $response->assertStatus(202);
    }

    public function  testCanDeleteEducationLevel(): void
    {
        $educationLevel = EducationLevel::factory()->create();

        $response = $this->deleteJson(
            route('education-levels.destroy', $educationLevel->id),
            $educationLevel->toArray()
        );

        $response->assertStatus(204);

        $this->assertDatabaseMissing('education_levels', $educationLevel->toArray());
    }
}
