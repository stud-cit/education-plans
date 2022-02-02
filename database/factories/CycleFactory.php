<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Cycle;

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
            'title' => $this->faker->sentence(),
            'number' => $this->faker->randomDigit(),
            'cycle_id' => $this->faker->randomDigit()
        ];
    }
}
