<?php

namespace Database\Factories;

use App\Enums\PropertyPurpose;
use App\Enums\PropertyStatus;
use App\Models\Agent;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Property> */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'agent_id' => Agent::factory(),
            'property_type_id' => PropertyType::factory(),
            'title' => fake()->unique()->sentence(4),
            'description' => fake()->paragraphs(3, true),
            'purpose' => fake()->randomElement(PropertyPurpose::cases()),
            'status' => PropertyStatus::Draft,
            'price' => fake()->randomFloat(2, 50000, 2000000),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postcode' => fake()->postcode(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'bedrooms' => fake()->numberBetween(0, 6),
            'bathrooms' => fake()->numberBetween(1, 4),
            'area' => fake()->randomFloat(2, 30, 1000),
            'is_featured' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => PropertyStatus::Published, 'published_at' => now()]);
    }

    public function future(): static
    {
        return $this->state(fn () => ['status' => PropertyStatus::Published, 'published_at' => now()->addDay()]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
