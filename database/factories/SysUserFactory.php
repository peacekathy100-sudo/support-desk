<?php

namespace Database\Factories;

use App\Models\SysUser;
use App\Models\UserRole;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class SysUserFactory extends Factory
{
    protected $model = SysUser::class;

    public function definition(): array
    {
        return [
            'user_name' => $this->faker->unique()->userName(),
            'check_number' => 'CHK-' . $this->faker->unique()->numerify('###'),
            'user_surname' => $this->faker->lastName(),
            'user_othername' => $this->faker->firstName(),
            'user_email' => $this->faker->unique()->safeEmail(),
            'user_password' => bcrypt('password'),
            'user_role' => UserRole::factory(),
            'dept_id' => Department::factory(),
            'user_status' => 'active',
            'user_gender' => $this->faker->randomElement(['Male', 'Female']),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_status' => 'inactive',
        ]);
    }
}
