<?php

namespace Database\Factories;

use App\Models\EducationLevel;
use App\Models\FormOrganization;
use App\Models\FormStudy;
use App\Models\Plan;
use App\Models\StudyTerm;
use App\Models\User;
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
            'title' => $this->faker->paragraph(1),
            'faculty_id' => $this->faker->randomDigit(),
            'department_id' => $this->faker->randomDigit(),
            'form_study_id' => FormStudy::factory(),
            'education_level_id' => EducationLevel::factory(),
            'year' => $this->faker->year(now()),
            'credits' => $this->faker->randomDigit(),
            'number_semesters' => $this->faker->randomDigit(),
            'speciality_id' => $this->faker->randomDigit(),
            'specialization_id' => $this->faker->randomDigit(),
            'education_program_id' => $this->faker->randomDigit(),
            'qualification_id' => $this->faker->randomDigit(),
            'field_knowledge_id' => $this->faker->randomDigit(),
            'form_organization_id' => FormOrganization::factory(),
            'study_term_id' => StudyTerm::factory(),
            'hours_weeks_semesters' => $this->getHoursWeeksSemesters(),
            'guid' => $this->faker->uuid(),
            'author_id' => User::factory(),
            'published' => true,
        ];
    }

    protected function getHoursWeeksSemesters()
    {
        return json_encode([
            ['hour' => 8, 'week' => 8, 'index' => 1, 'course' => 1, 'semester' => 1],
            ['hour' => 8, 'week' => 8, 'index' => 1, 'course' => 1, 'semester' => 2],
            ['hour' => 8, 'week' => 8, 'index' => 1, 'course' => 2, 'semester' => 3],
            ['hour' => 8, 'week' => 8, 'index' => 1, 'course' => 2, 'semester' => 4],
            ['hour' => 8, 'week' => 8, 'index' => 1, 'course' => 3, 'semester' => 5],
            ['hour' => 8, 'week' => 8, 'index' => 1, 'course' => 3, 'semester' => 6],
            ['hour' => 8, 'week' => 8, 'index' => 1, 'course' => 4, 'semester' => 7],
            ['hour' => 8, 'week' => 8, 'index' => 1, 'course' => 4, 'semester' => 8],
        ]);
    }
}
