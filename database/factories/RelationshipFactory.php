<?php

namespace Database\Factories;

use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Keeper;
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
            'keeper_id' => Keeper::factory(),
            'child_id' => Child::factory(),
            'relationship' => $this->faker->randomElement(Relationship::cases()),
            'is_authorized_guardian' => $this->faker->boolean(),
            'notes' => $this->faker->text(),
        ];
    }
}
