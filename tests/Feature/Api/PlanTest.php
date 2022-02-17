<?php

namespace Tests\Feature\Api;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
        $plan = Plan::factory()->create();

        $response = $this->getJson(route('plans.show', $plan));

        $response->assertOk()->assertExactJson([
            'data' => [
                'id' => $plan->id,
                'title' => $plan->title,
                'faculty' => $plan->faculty,
                'department' => $plan->department,
                'year' => $plan->year,
                'form_study' => $plan->formStudy->title,
                'education_level' => $plan->educationLevel->title,
                'credits' => $plan->credits,
                'number_semesters' => $plan->number_semesters,
                'specialization_id' => $plan->specialization_id,
                'specialization' => $plan->specialization,
                'education_program_id' => $plan->education_program_id,
                'qualification_id' => $plan->qualification_id,
                'field_knowledge_id' => $plan->field_knowledge_id,
                'count_hours' => $plan->count_hours,
                'count_week' => $plan->count_week,
                'created_at' => $plan->created_at,
            ]
        ]);
    }
}
