<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Http\Resources\CycleShowResource;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function testCanDeletePlan()
    {
        $this->actingAsUser();

        $plan = Plan::factory()->create();

        $response = $this->deleteJson(route('plans.destroy', $plan));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('plans', $plan->toArray());
    }

    public function testCanGetAllPlans()
    {
        $this->actingAsUser();

        $plan = Plan::factory()->create();

        $response = $this->getJson(route('plans.index'));

        $response->assertOk();

        $response->assertJson([
            'data' => [
                [
                    'id' => $plan->id,
                    'title' => $plan->title,
                    'faculty' => $plan->facultyName,
                    'faculty_id' => $plan->faculty_id,
                    'short_faculty' => $plan->shortFacultyName,
                    'department' => $plan->departmentName,
                    'year' => $plan->year,
                    'created_at' => $plan->created_at,
                ]
            ]
        ]);
    }

    public function testCanShowPlan()
    {
        $this->actingAsUser();

        $plan = Plan::factory()->hasCycles(7)->create();

        $response = $this->getJson(route('plans.show', $plan));

        $response->assertOk()->assertExactJson([
            'data' => [
                'id' => $plan->id,
                'title' => $plan->title,
                'faculty' => $plan->facultyName,
                'department' => $plan->departmentName,
                'year' => $plan->year,
                'form_study' => $plan->formStudy,
                'form_organization' => $plan->formOrganization,
                'education_level' => $plan->educationLevel,
                'study_term' => $plan->studyTerm,
                'form_organization_id' => $plan->formOrganization ? $plan->formOrganization->id : null,
                'credits' => $plan->credits,
                'number_semesters' => $plan->number_semesters,
                'speciality' => $plan->speciality_id_name,
                'specialization' => $plan->specialization_id_name,
                'education_program' => $plan->education_program_id_name,
                'qualification' => $plan->qualification_id_name,
                'field_knowledge' => $plan->field_knowledge_id_name,
                'cycles' => CycleShowResource::collection($plan->cycles->whereNull('cycle_id')),
                'hours_weeks_semesters' => $plan->hours_weeks_semesters ?
                    json_decode($plan->hours_weeks_semesters) : null,
                'schedule_education_process' => $plan->schedule_education_process ?
                    json_decode($plan->schedule_education_process) : null,

            ]
        ]);
    }

}
