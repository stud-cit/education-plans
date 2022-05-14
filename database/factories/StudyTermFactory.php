<?php

namespace Database\Factories;

use App\Models\StudyTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudyTermFactory extends Factory
{

    protected $model = StudyTerm::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->unique()->word(),
            'year' => $this->faker->randomDigit(),
            'month' => $this->faker->randomDigit(),
            'course' => $this->faker->randomDigit(),
            'module' => $this->faker->randomDigit(),
            'semesters' => $this->faker->randomDigit(),
        ];
    }
}
