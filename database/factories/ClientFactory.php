<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'client_code' => strtoupper($this->faker->unique()->bothify('CLI-####')),
            'client_name' => $this->faker->company(),
            'client_email' => $this->faker->companyEmail(),
            'client_contact' => $this->faker->phoneNumber(),
            'client_address' => $this->faker->address(),
            'is_active' => true,
        ];
    }
}
