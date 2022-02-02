<?php

namespace Database\Factories;

use App\Models\Cycle;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'credit' => $this->faker->randomNumber(),
            'cycle_id' => function (array $attributes) {
                return Cycle::find(1);
            },
        ];
    }
}
