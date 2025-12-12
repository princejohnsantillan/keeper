<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Keeper;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'child_id' => Child::factory(),
            'checkin_keeper_id' => Keeper::factory(),
            'checkin_gatepass_id' => Gatepass::factory(),
            'checked_in_at' => now(),
            'checkout_keeper_id' => null,
            'checkout_gatepass_id' => null,
            'checked_out_at' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function checkedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'checkout_keeper_id' => Keeper::factory(),
            'checkout_gatepass_id' => Gatepass::factory(),
            'checked_out_at' => now(),
        ]);
    }
}
