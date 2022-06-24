<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Position;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SignatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'plan_id' => Plan::factory(),
            'position_id' => Position::factory(),
            'asu_id' => Str::uuid(),
        ];
    }
}
