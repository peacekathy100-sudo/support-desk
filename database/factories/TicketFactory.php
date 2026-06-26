<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\SysUser;
use App\Models\TicketCategory;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'ticket_number' => Ticket::generateTicketNumber(),
            'created_by' => SysUser::factory(),
            'assigned_to' => SysUser::factory(),
            'category_id' => TicketCategory::factory(),
            'subject' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['low', 'normal', 'high', 'urgent']),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'on_hold', 'resolved', 'closed']),
            'resolution_note' => null,
            'resolved_by' => null,
            'resolved_at' => null,
            'closed_at' => null,
            'due_at' => $this->faker->optional()->dateTimeBetween('now', '+7 days'),
            'reopened_by' => null,
            'reopened_at' => null,
            'created_from_dept' => 1,
            'client_id' => Client::factory(),
            'chargeable' => $this->faker->boolean(30),
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
            'due_at' => now()->subDays(1),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }
}
