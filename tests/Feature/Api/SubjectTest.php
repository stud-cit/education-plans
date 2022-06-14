<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'subjects.';
    private $table = 'subjects';

    public function testCanStoreSubject()
    {
        $this->actingAsUser();
        $subject = Subject::factory()->make();

        $response = $this->postJson(route("{$this->route}store"), $subject->toArray());

        $response->assertStatus(201)->assertJson(['message' => __('messages.Created')]);
    }

    public function testCanUpdateSubject(): void
    {
        $this->actingAsUser();

        $subject = Subject::factory()->make();
        $existsSubject = Subject::factory()->create();

        $response = $this->putJson(route("{$this->route}update", $existsSubject->id), $subject->toArray());

        $response->assertStatus(200)->assertJson(['message' => __('messages.Updated')]);
    }

    public function testCanDelete()
    {
        $this->actingAsUser();

        $subject = Subject::factory()->create();

        $response = $this->deleteJson(route("{$this->route}destroy", $subject->id));

        $response->assertStatus(200)->assertJson(['message' => __('messages.Deleted')]);
        $this->assertDatabaseMissing($this->table, $subject->toArray());
    }
}
