<?php

namespace Database\Factories;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Agent> */
class AgentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'bio' => fake()->paragraph(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'is_active' => true,
        ];
    }
}
