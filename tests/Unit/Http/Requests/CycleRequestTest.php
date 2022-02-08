<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Models\Cycle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CycleRequestTest extends TestCase
{
    use RefreshDatabase;

    private $route = 'cycles.';

    public function testTitleIsRequired()
    {
        $validatedTypeField = 'title';
        $brokenRule = null;
        
        $cycle = Cycle::factory()->make([$validatedTypeField => $brokenRule]);

        $this->postJson(
            route($this->route . 'store'),
            $cycle->toArray()
        )->assertJsonValidationErrors($validatedTypeField);

        // update
        $existCycle = Cycle::factory()->create();
        $newCycle = Cycle::factory()->make([$validatedTypeField => $brokenRule]);
        
        $this->patchJson(
            route($this->route . 'update', $existCycle),
            $newCycle->toArray()
        )->assertJsonValidationErrors($validatedTypeField);
    }

    public function testSubCycleIdRequired()
    {
        $validatedTypeField = 'cycle_id';
        $brokenRule = null;

        $subCycle = Cycle::factory()->make([$validatedTypeField => $brokenRule]);

        $this->postJson(
            route($this->route . 'sub.store'),
            $subCycle->toArray()
        )->assertJsonValidationErrors($validatedTypeField);
        // update
        $existSubCycle = Cycle::factory()->create([
            'cycle_id' => 1
        ]);
        $newlySubCycle = Cycle::factory()->make([$validatedTypeField => $brokenRule]);

        $this->patchJson(
            route($this->route . 'sub.update', $existSubCycle),
            $newlySubCycle->toArray()
        )->assertJsonValidationErrors($validatedTypeField);

    }
}
