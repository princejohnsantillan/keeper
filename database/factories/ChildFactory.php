<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Child>
 */
final class ChildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'nickname' => $this->faker->optional()->firstName(),
            'birth_date' => $this->faker->dateTimeBetween('-12 years', '-1 year')->format('Y-m-d'),
            'gender' => $this->faker->boolean(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Child $child): void {
            $child->addMediaFromUrl('https://i.pravatar.cc/300?u='.$child->id)
                ->toMediaCollection('avatar');
        });
    }
}
