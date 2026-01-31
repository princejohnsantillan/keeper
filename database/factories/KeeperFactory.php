<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\KeeperRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Keeper>
 */
final class KeeperFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'organization_id' => Organization::factory(),
            'role' => KeeperRole::Gatekeeper,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => KeeperRole::Admin,
        ]);
    }

    public function gatekeeper(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => KeeperRole::Gatekeeper,
        ]);
    }
}
