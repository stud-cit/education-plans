<?php

namespace Tests\Feature\Api\AdminDepartmentPostgraduate;

use App\Models\Plan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PlansPostGraduateTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
        $this->seed([RoleSeeder::class]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanGetAllPlans()
    {
        $postgraduateAdmin = User::factory()->adminDepartmentPostgraduate()->create();

        // Authenticate as the admin before creating the plan so the Plan model's creating event works
        // move to store method without emvent creating event
        $this->actingAs($postgraduateAdmin);

        $plan = Plan::factory()->create();

        $response = $this->getJson(route('plans.index'));

        $response->assertOk();

        // $response->assertJson([
        //     'data' => [
        //         [
        //             'id' => $plan->id,
        //             'title' => $plan->title,
        //             'faculty' => $plan->facultyName,
        //             'faculty_id' => $plan->faculty_id,
        //             'short_faculty' => $plan->shortFacultyName,
        //             'department' => $plan->departmentName,
        //             'year' => $plan->year,
        //             'created_at' => $plan->created_at,
        //         ]
        //     ]
        // ]);
    }
}
