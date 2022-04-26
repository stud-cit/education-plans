<?php

namespace Database\Factories;

use App\Models\FormOrganization;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormOrganizationFactory extends Factory
{
    protected $model = FormOrganization::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->unique()->word()
        ];
    }
}
