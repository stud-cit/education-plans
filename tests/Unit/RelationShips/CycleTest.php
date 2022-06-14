<?php declare(strict_types=1);

namespace Tests\Unit\RelationShips;

use Tests\TestCase;
use App\Models\Cycle;
use App\Models\Credit;
use Database\Seeders\CycleSeeder;
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
        $this->actingAsUser();

        $cycle = Cycle::factory()->create();

        $this->assertModelExists($cycle);
    }
}
