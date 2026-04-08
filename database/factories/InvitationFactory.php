<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Child;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\ReadableCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invitation>
 */
final class InvitationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'code' => ReadableCode::generate(),
            'activity_id' => Activity::factory(),
            'invitee_fullname' => fake()->name(),
            'invitee_email' => fake()->safeEmail(),
            'invitee_phone' => null,
            'used_on_child_id' => null,
            'notes' => null,
            'message_id' => null,
            'created_by' => User::factory(),
        ];
    }

    public function used(?Child $child = null): static
    {
        return $this->state(fn (): array => [
            'used_on_child_id' => $child?->id ?? Child::factory(),
        ]);
    }
}
