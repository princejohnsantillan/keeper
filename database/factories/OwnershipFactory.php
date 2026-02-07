<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ownership>
 */
final class OwnershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_type' => User::class,
            'owner_id' => User::factory(),
            'model_type' => Child::class,
            'model_id' => Child::factory(),
        ];
    }
}
