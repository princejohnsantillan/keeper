<?php

declare(strict_types=1);

namespace App\Actions;

use App\AuthUser;
use App\Models\Guardian;

final class CreateGuardianAction
{
    /**
     * Create a guardian and sync their children relationships.
     *
     * @param  array<string, mixed>  $guardianData
     * @param  array<int|string, array{relationship: string}>  $syncData
     */
    public function __invoke(array $guardianData, array $syncData): Guardian
    {
        $guardianData['owner_id'] = AuthUser::userId();

        $guardian = Guardian::query()->create($guardianData);

        $guardian->children()->sync($syncData);

        return $guardian;
    }
}
