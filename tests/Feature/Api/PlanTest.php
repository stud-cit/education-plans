<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlanTest extends TestCase
{
    use RefreshDatabase;

    public function testCanDeletePlan()
    {
        $plan = Plan::factory()->create();

        $response = $this->deleteJson(route('plans.destroy', $plan));

        $response->assertStatus(204);
        $this->assertDatabaseMissing('plans', $plan->toArray());
    }

    public function testCanGetAllPlans()
    {
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
        $plan = Plan::factory()->hasCycles(7)->create();

        $response = $this->getJson(route('plans.show', $plan));

        $response->assertOk()->assertExactJson([
            'data' => [
                'id' => $plan->id,
                'title' => $plan->title,
                'faculty' => $plan->facultyName,
                'department' => $plan->departmentName,
                'year' => $plan->year,
                'form_study' => $plan->formStudy->title,
                'education_level' => $plan->educationLevel->title,
                'form_organization' => $plan->formOrganization->title,
                'credits' => $plan->credits,
                'number_semesters' => $plan->number_semesters,
                'specialization_id' => $plan->specialization_id,
                'specialization' => $plan->specialization,
                'education_program_id' => $plan->education_program_id,
                'qualification_id' => $plan->qualification_id,
                'field_knowledge_id' => $plan->field_knowledge_id,
                'cycles' => \App\Helpers\Tree::makeTree($plan->cycles),
                'count_hours' => $plan->count_hours,
                'count_week' => $plan->count_week,
                'created_at' => $plan->created_at,
            ]
        ]);
    }

    public function testCanCopyPlan()
    {
        $plan = Plan::factory()->create();

        $response = $this->post(route('plans.copy', $plan));

        $response->assertStatus(201);

        //$this->assertDatabaseHas('plans', );
    }
}
