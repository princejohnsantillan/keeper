<?php

namespace Database\Factories;

use App\Models\Guardian;
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
            'middle_name' => $this->faker->randomLetter(),
            'last_name' => $this->faker->lastName(),
            'nickname' => $this->faker->name(),
            'birth_date' => $this->faker->date(),
            'gender' => $this->faker->boolean(),
            'notes' => $this->faker->text(),
            'primary_keeper_id' => Guardian::factory(),
        ];
    }
}
