<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Child>
 */
class ChildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'nickname' => $this->faker->optional()->firstName(),
            'birth_date' => $this->faker->dateTimeBetween('-12 years', '-1 year')->format('Y-m-d'),
            'gender' => $this->faker->boolean(),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
