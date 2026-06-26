<?php
namespace Database\Factories;
use App\Models\TicketCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
class TicketCategoryFactory extends Factory
{
    protected $model = TicketCategory::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'color' => $this->faker->hexColor(),
            'sla_hours' => $this->faker->randomElement([4, 8, 24, 48, 72]),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
