<?php

namespace Tests\Feature\Api;

use App\Models\CatalogSelectiveSubject;
use App\Models\CatalogSubject;
use App\Models\SubjectVerification;
use App\Models\User;
use App\Models\VerificationStatuses;
use Database\Seeders\CatalogGroupSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SelectiveDisciplineSeeder;
use Database\Seeders\SubjectVerificationStatusesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogSujectTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed([
            RoleSeeder::class,
            SelectiveDisciplineSeeder::class,
            CatalogGroupSeeder::class,
            SubjectVerificationStatusesSeeder::class,
        ]);
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_index(): void
    {
        $user = User::factory()->department()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('catalog-subjects.index'));

        $response->assertStatus(200);
    }

    public function test_store(): void
    {
        $user = User::factory()->department()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('catalog-subjects.store'), [
            'year' => 2024,
            'group_id' => 1
        ]);

        $response->assertStatus(201);
    }

    public function test_guest_cannot_store(): void
    {
        $user = User::factory()->guest()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('catalog-subjects.store'), [
            'year' => 2024,
            'group_id' => 1
        ]);

        $response->assertStatus(403);
    }

    public function test_guest_can_get_years(): void
    {
        $user = User::factory()->guest()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('catalog-subjects.get-years'));

        $response->assertStatus(200);
    }

    public function test_guest_can_get_catalogs(): void
    {
        $user = User::factory()->guest()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('catalog-subjects.catalog-titles'));

        $response->assertStatus(200);
    }

    public function test_can_generate_pdf(): void
    {
        $user = User::factory()->department()->create();
        $this->actingAs($user);

        $catalogSubject = CatalogSubject::create([
            'year' => now()->year,
            'group_id' => 1,
            'faculty_id' => $user->faculty_id,
            'department_id' => $user->department_id,
            'selective_discipline_id' => 1,
        ]);

        $subject = CatalogSelectiveSubject::create([
            'catalog_subject_id' => $catalogSubject->id,
            'user_id' => $user->id,
            'asu_id' => 1,
            'title' => 'Test subject',
            'title_en' => 'Test subject',
            'list_fields_knowledge' => json_encode(['label' => '', 'type_name' => '', 'list' => []]),
            'faculty_id' => $user->faculty_id,
            'department_id' => $user->department_id,
            'general_competence' => 'Test competence',
            'learning_outcomes' => 'Test outcomes',
            'types_educational_activities' => 'Lecture',
            'number_acquirers' => '1',
            'entry_requirements_applicants' => 'None',
            'limitation' => json_encode(['label' => '', 'semesters' => []]),
            'published' => true,
            'need_verification' => true,
        ]);

        foreach (VerificationStatuses::whereType('subject')->pluck('id') as $statusId) {
            SubjectVerification::create([
                'user_id' => $user->id,
                'verification_status_id' => $statusId,
                'subject_id' => $subject->id,
                'status' => true,
            ]);
        }

        $data = [
            'year' => now()->year,
            'group_id' => 1,
        ];

        $response = $this->getJson(route('catalog-subjects.generate-pdf', $data));

        $response->assertStatus(200);
    }
}
