<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Guardian;
use App\Models\Relationship;
use Illuminate\Support\Collection;

final class DetachGuardianFromChildrenAction
{
    /**
     * Detach a guardian from specific children by removing their relationships.
     * This does not delete the guardian, only removes the relationships with the given children.
     *
     * @param  Collection<int, int>|array<int, int>  $childIds
     */
    public function __invoke(Guardian $guardian, Collection|array $childIds): void
    {
        Relationship::query()
            ->where('guardian_id', $guardian->id)
            ->whereIn('child_id', $childIds)
            ->delete();
    }
}
