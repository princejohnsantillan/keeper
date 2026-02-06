<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Child;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationNote>
 */
final class OrganizationNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'notable_type' => Child::class,
            'notable_id' => (string) Str::ulid(),
            'note' => $this->faker->sentence(),
        ];
    }
}
