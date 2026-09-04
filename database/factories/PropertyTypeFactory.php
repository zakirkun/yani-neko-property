<?php

namespace Database\Factories;

use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<PropertyType> */
class PropertyTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = ucfirst(fake()->unique()->words(2, true));

        return ['name' => $name, 'slug' => Str::slug($name), 'description' => fake()->sentence()];
    }
}
