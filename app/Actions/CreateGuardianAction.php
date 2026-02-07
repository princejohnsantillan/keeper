<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Guardian;
use App\Models\Organization;
use App\Models\User;

final class CreateGuardianAction
{
    public function __construct(
        private CreateOwnershipAction $createOwnershipAction,
    ) {}

    /**
     * Create a guardian and sync their children relationships.
     *
     * @param  array<string, mixed>  $guardianData
     * @param  array<int|string, array{relationship: string}>  $syncData
     */
    public function __invoke(array $guardianData, array $syncData, User|Organization $owner): Guardian
    {
        $guardian = Guardian::query()->create($guardianData);

        ($this->createOwnershipAction)($guardian, $owner);

        $guardian->children()->sync($syncData);

        return $guardian;
    }
}
