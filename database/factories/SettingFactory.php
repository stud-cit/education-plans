<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'key' => $this->faker->uuid,
            'title' => $this->faker->word(),
            'value' => $this->faker->randomDigit()
        ];
    }
}
