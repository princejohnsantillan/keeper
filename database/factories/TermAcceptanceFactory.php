<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Guardian;
use App\Models\Term;
use App\Models\TermAcceptance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TermAcceptance>
 */
final class TermAcceptanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'term_id' => Term::factory(),
            'guardian_id' => Guardian::factory(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'accepted_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
