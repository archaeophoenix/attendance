<?php

namespace Database\Factories;

use App\Models\Employees;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeesFactory extends Factory
{
    protected $model = Employees::class;

    public function definition(): array
    {
        return [
            'code' => 'EMP-' . $this->faker->unique()->numberBetween(10000, 99999),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'position' => $this->faker->randomElement(['Staff', 'Admin']),
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
            'join_date' => $this->faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
        ];
    }
}
