<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cycle;
use App\Models\ListCycle;

class CycleFactory extends Factory
{

    protected $model = Cycle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 'title' => $this->faker->sentence(),
            'cycle_id' => null,
            'list_cycle_id' => ListCycle::factory(),
            'credit' => $this->faker->randomDigit(),
        ];
    }
}
