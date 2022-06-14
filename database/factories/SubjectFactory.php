<?php

namespace Database\Factories;

use App\Models\Cycle;
use App\Models\SelectiveDiscipline;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{

    protected $model = Subject::class;

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
            'selective_discipline_id' => SelectiveDiscipline::factory(),
            'credits' => $this->faker->randomDigitNotZero(),
            'hours' => $this->faker->randomDigitNotZero(),
            'practices' => $this->faker->randomDigitNotZero(),
            'laboratories' => $this->faker->randomDigitNotZero()
        ];
    }
}
