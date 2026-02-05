<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gatepass>
 */
final class GatepassFactory extends Factory
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
            'guardian_id' => Guardian::factory(),
            'child_id' => Child::factory(),
            'activity_id' => Activity::factory(),
            'code' => Str::upper(Str::random(6)),
            'term_acceptance_id' => null,
        ];
    }
}
