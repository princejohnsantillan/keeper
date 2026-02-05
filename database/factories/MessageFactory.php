<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Message;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
final class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->words(3, true),
            'content' => fake()->paragraphs(2, true),
            'created_by' => User::factory(),
        ];
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes): array => [
            'archived_at' => now(),
        ]);
    }
}
