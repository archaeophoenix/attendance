<?php

namespace Database\Factories;

use App\Models\Employees;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeesFactory extends Factory
{
    protected $model = Employees::class;

    protected static $sequence = 1;

    public function definition(): array
    {
        $code = 'Kar-' . str_pad(self::$sequence++, 5, '0', STR_PAD_LEFT);

        return [
            'code' => $code,
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'position' => $this->faker->randomElement(['Staff', 'Admin']),
            'status' => $this->faker->randomElement(['Active', 'Inactive']),
            'join_date' => $this->faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
        ];
    }
}
