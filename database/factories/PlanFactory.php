<?php

namespace Database\Factories;

use App\Models\EducationLevel;
use App\Models\FormStudy;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'faculty_id' => $this->faker->randomDigit(),
            'department_id' => $this->faker->randomDigit(),
            'form_study_id' => FormStudy::factory(),
            'education_level_id' => EducationLevel::factory(),
            'year' => $this->faker->year(now()),
            'credits' => $this->faker->randomDigit(),
            'number_semesters' => $this->faker->randomDigit(),
            'specialization_id' => $this->faker->randomDigit(),
            'specialization' => $this->faker->word(),
            'plans.education_program_id' => $this->faker->randomDigit(),
        ];
    }
}
