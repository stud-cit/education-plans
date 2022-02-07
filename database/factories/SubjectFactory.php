<?php

namespace Database\Factories;

use App\Models\Cycle;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'asu_id' => $this->faker->uuid,
            'cycle_id' => Cycle::factory(),
            'title' => $this->faker->text(255),
            'credits' => $this->faker->randomDigit(),
            'hours' => $this->faker->randomDigit(),
            'practices' => $this->faker->randomDigit(),
            'laboratories' => $this->faker->randomDigit()
        ];
    }
}
