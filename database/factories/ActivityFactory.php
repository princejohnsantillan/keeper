<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
final class ActivityFactory extends Factory
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
            'location' => $this->faker->address(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+2 hours'),
            'publish_at' => now(),
            'notes' => $this->faker->optional()->sentence(),
            'organization_id' => Organization::factory(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the activity is a draft (not published).
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'publish_at' => null,
        ]);
    }
}
