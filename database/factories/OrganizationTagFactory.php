<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Child;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrganizationTag>
 */
final class OrganizationTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'taggable_type' => Child::class,
            'taggable_id' => (string) Str::ulid(),
            'name' => $this->faker->word(),
        ];
    }
}
