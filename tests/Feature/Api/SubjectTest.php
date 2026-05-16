<?php

namespace Tests\Feature\Api;

use App\Models\Plan;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\FormOrganizationSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'subjects.';
    private $table = 'subjects';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\EnsureCabinetTokenIsValid::class);
        $this->seed([
            RoleSeeder::class,
            FormOrganizationSeeder::class,
            \Database\Seeders\FormControlSeeder::class,
            \Database\Seeders\IndividualTaskSeeder::class,
        ]);
    }

    public function testCanStoreSubject(): void
    {
        $department = User::factory()->department()->create();
        $this->actingAs($department);

        $plan = Plan::factory()->create(['department_id' => $department->department_id, 'form_organization_id' => 1]);
        $subject = Subject::factory()->make();
        $data = array_merge($subject->toArray(), [
            'plan_id' => $plan->id,
            'hours_modules' => [
                [
                    'course' => 1,
                    'form_control_id' => 1,
                    'hour' => 8,
                    'individual_task_id' => 1,
                    'module' => 1,
                    'semester' => 1,
                ],
            ],
            'semesters_credits' => [
                [
                    'course' => 1,
                    'credit' => 1,
                    'semester' => 1,
                ],
            ],
        ]);

        $response = $this->postJson(route("{$this->route}store"), $data);

        $response->assertStatus(201)->assertJson(['message' => __('messages.Created')]);
    }

    public function testCanUpdateSubject(): void
    {
        $department = User::factory()->department()->create();
        $this->actingAs($department);
        $plan = Plan::factory()->create(['department_id' => $department->department_id, 'form_organization_id' => 1]);
        $subject = Subject::factory()->make(['asu_id' => null, 'selective_discipline_id' => null]);
        $existsSubject = Subject::factory()->create();

        $data = array_merge($subject->toArray(), ['plan_id' => $plan->id]);

        $response = $this->putJson(route("{$this->route}update", $existsSubject), $data);

        $response->assertStatus(200)->assertJson(['message' => __('messages.Updated')]);
    }

    public function testCanDelete()
    {
        $department = User::factory()->department()->create();
        $this->actingAs($department);

        $subject = Subject::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $subject));

        $response->assertStatus(200)->assertJson(['message' => __('messages.Deleted')]);
        $this->assertDatabaseMissing($this->table, $subject->getAttributes());
    }
}
