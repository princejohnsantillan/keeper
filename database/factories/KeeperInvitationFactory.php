<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\KeeperRole;
use App\Enums\KeeperStatus;
use App\Models\Keeper;
use App\Models\KeeperInvitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<KeeperInvitation>
 */
final class KeeperInvitationFactory extends Factory
{
    protected $model = KeeperInvitation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory()->invited(),
            'invited_by' => User::factory(),
            'role' => KeeperRole::Gatekeeper,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ];
    }

    public function withPendingKeeper(): static
    {
        return $this->afterCreating(function (KeeperInvitation $invitation): void {
            Keeper::query()->create([
                'user_id' => $invitation->user_id,
                'organization_id' => $invitation->organization_id,
                'role' => $invitation->role,
                'status' => KeeperStatus::Pending,
            ]);
        });
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'accepted_at' => now(),
        ]);
    }
}
