<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Guardian;

final class UpdateGuardianAction
{
    /**
     * Update a guardian and optionally sync children relationships.
     *
     * @param  array<string, mixed>  $guardianData
     * @param  array<string, array{relationship: string}>|null  $childrenSyncData  Child IDs (ULIDs) mapped to pivot data
     */
    public function __invoke(
        Guardian $guardian,
        array $guardianData,
        ?array $childrenSyncData = null,
    ): Guardian {
        $guardian->update($guardianData);

        if ($childrenSyncData !== null) {
            $guardian->children()->sync($childrenSyncData);
        }

        return $guardian;
    }
}
