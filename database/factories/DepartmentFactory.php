<?php
namespace Database\Factories;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
class DepartmentFactory extends Factory
{
    protected $model = Department::class;
    public function definition(): array
    {
        return [
            'dept_name' => $this->faker->unique()->company(),
            'dept_code' => strtoupper($this->faker->unique()->bothify('DEPT-###')),
            'is_active' => true,
        ];
    }
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
