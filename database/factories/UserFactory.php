<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'asu_id' => Str::random(10),
            'faculty_id' => $this->faker->randomDigit(),
            'department_id' => $this->faker->randomDigit(),
            'role_id' => $this->faker->biasedNumberBetween(1, 6),
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            // 'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    public function withRole(int $roleId): Factory
    {
        return $this->state(function (array $attributes) use ($roleId) {
            return [
                'role_id' => $roleId,
            ];
        });
    }

    public function admin(): Factory
    {
        return $this->state(fn() => ['role_id' => User::ADMIN]);
    }

    public function guest(): Factory
    {
        return $this->state(fn() => ['role_id' => User::GUEST]);
    }

    public function department(): Factory
    {
        return $this->state(fn() => ['role_id' => User::DEPARTMENT]);
    }

    public function adminDepartmentPostgraduate(): Factory
    {
        return $this->state(fn() => ['role_id' => User::ADMIN_DEPARTMENT_POSTGRADUATE]);
    }
}
