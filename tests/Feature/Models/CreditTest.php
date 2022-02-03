<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\Credit;
use Database\Seeders\CycleSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A is model Credit Exists.
     *
     * @return void
     */
    public function testIsModelCreditExists()
    {
        $this->seed(CycleSeeder::class);

        $credit = Credit::factory()->create();

        $this->assertModelExists($credit);
    }
}
