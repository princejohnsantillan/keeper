<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = $this->faker->dateTimeBetween('now', '+1 month');

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->sentence(),
            'location' => $this->faker->optional()->address(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+2 hours'),
            'notes' => $this->faker->optional()->sentence(),
            'organization_id' => Organization::factory(),
            'created_by' => User::factory(),
        ];
    }
}
