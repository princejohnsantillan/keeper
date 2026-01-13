<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;

final class DetachChildFromGuardianAction
{
    /**
     * Detach a child from a guardian by removing their relationship.
     * This does not delete the child, only removes the relationship.
     */
    public function __invoke(Child $child, Guardian $guardian): void
    {
        Relationship::query()
            ->where('child_id', $child->id)
            ->where('guardian_id', $guardian->id)
            ->delete();
    }
}
