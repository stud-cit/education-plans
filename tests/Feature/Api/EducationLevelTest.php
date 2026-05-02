<?php

namespace Tests\Feature\Api;

use App\Models\EducationLevel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationLevelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
        $this->seed([RoleSeeder::class]);
    }

    public function testGuestCannotRestoreTreshedEducationLevels(): void
    {
        $user = User::factory()->guest()->create();

        $educationLevel = EducationLevel::factory()->trashed()->create();

        $response = $this->actingAs($user)->patchJson(route('education-levels.restore', $educationLevel->id));

        $response->assertStatus(403)->assertJsonStructure(['message']);
    }

    public function testAdminCanRestoreTreshedEducationLevels(): void
    {
        $user = User::factory()->admin()->create();

        $educationLevel = EducationLevel::factory()->trashed()->create();

        $response = $this->actingAs($user)->patchJson(route('education-levels.restore', $educationLevel->id));

        $response->assertStatus(201)->assertJsonStructure(['message']);
    }

    public function testCanGetAllEducationLevels(): void
    {
        $user = User::factory()->admin()->create();

        EducationLevel::factory()->create();

        $response = $this->actingAs($user)->get(route('education-levels.index'));

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
        $user = User::factory()->admin()->create();

        $educationLevel = EducationLevel::factory()->make();

        $response = $this->actingAs($user)->postJson(route('education-levels.store'), $educationLevel->toArray());

        $response->assertCreated();

        $this->assertDatabaseHas('education_levels', $educationLevel->toArray());
    }

    public function testCanShowEducationLevel(): void
    {
        $user = User::factory()->admin()->create();

        $educationLevel = EducationLevel::factory()->create();

        $response = $this->actingAs($user)->getJson(route('education-levels.show', $educationLevel->id));

        $response->assertok();

        $response->assertJson([
            'data' => [
                'title' => $educationLevel->title
            ]
        ]);
    }

    public function testCanUpdateEducationLevel(): void
    {
        $user = User::factory()->admin()->create();

        $educationLevel = EducationLevel::factory()->create();
        $newlyEducationLevel = EducationLevel::factory()->make();

        $response = $this->actingAs($user)->putJson(
            route('education-levels.update', $educationLevel->id),
            ['title' => $newlyEducationLevel->title]
        );

        $response->assertStatus(200);
    }

    public function  testCanDeleteEducationLevel(): void
    {
        $user = User::factory()->admin()->create();

        $educationLevel = EducationLevel::factory()->create();

        $response = $this->actingAs($user)->deleteJson(
            route('education-levels.destroy', $educationLevel->id),
            $educationLevel->toArray()
        );

        $response->assertStatus(201);

        $this->assertDatabaseMissing('education_levels', $educationLevel->toArray());
    }
}
