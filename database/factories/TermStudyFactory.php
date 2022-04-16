<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TermStudyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'year' => $this->faker->randomDigit(),
            'month' => $this->faker->randomDigit(),
            'course' => $this->faker->randomDigit(),
            'module' => $this->faker->randomDigit(),
        ];
    }
}
