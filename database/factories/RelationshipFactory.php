<?php

namespace Database\Factories;

use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Relationship>
 */
class RelationshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'keeper_id' => Guardian::factory(),
            'child_id' => Child::factory(),
            'relationship' => $this->faker->randomElement(Relationship::cases()),
            'notes' => $this->faker->text(),
        ];
    }
}
