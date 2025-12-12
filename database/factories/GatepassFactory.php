<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gatepass>
 */
class GatepassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'guardian_id' => Guardian::factory(),
            'child_id' => Child::factory(),
            'service_id' => Service::factory(),
            'code' => Str::upper(Str::random(6)),
        ];
    }
}
