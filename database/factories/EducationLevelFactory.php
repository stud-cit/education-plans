<?php

namespace Database\Factories;

use App\Models\EducationLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationLevelFactory extends Factory
{
    protected $model = EducationLevel::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word(),
        ];
    }
}
