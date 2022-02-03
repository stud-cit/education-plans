<?php

namespace Tests\Feature\Models;

use App\Models\Cycle;
use App\Models\Discipline;
use Database\Seeders\CycleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DisciplineTest extends TestCase
{
    use RefreshDatabase;

    public function testIsModelDisciplineExists()
    {
        $this->seed(CycleSeeder::class);

        $model = Discipline::factory()->create();

        $this->assertModelExists($model);

        return $model->cycle_id;
    }

    /**
     * @depends testIsModelDisciplineExists
     * @return void
     */
    public function testIsPossibleDeleteRelationShip($id)
    {
        $this->seed(CycleSeeder::class);

        $discipline = Discipline::factory()->create();

        $cycle = Cycle::find($discipline->cycle_id);
        $cycle->delete();
        $this->assertDeleted($cycle);
    }
}
