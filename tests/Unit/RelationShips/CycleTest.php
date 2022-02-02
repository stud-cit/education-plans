<?php declare(strict_types=1);

namespace Tests\Unit\RelationShips;

use Tests\TestCase;
use App\Models\Cycle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CycleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create model Cycle.
     *
     * @return void
     */
    public function testCreateCycleModel(): void
    {
        $cycle = Cycle::factory()->create();
        
        $this->assertModelExists($cycle);
    }
}
