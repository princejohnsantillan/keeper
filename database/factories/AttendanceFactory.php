<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Keeper;
use App\Models\Service;
use App\Models\User;
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

            'checkin_processed_by' => User::factory(),
            'checked_in_by' => Keeper::factory(),
            'checked_in_at' => now(),

            'checkout_processed_by' => User::factory(),
            'checked_out_by' => Keeper::factory(),
            'checked_out_at' => now(),

            'notes' => $this->faker->text(),
        ];
    }
}
