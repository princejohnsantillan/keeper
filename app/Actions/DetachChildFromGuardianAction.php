<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;
use App\Models\Guardian;

final class DetachChildFromGuardianAction
{
    /**
     * Soft delete a child. Relationships are preserved for reporting purposes.
     */
    public function __invoke(Child $child, Guardian $guardian): void
    {
        $child->delete();
    }
}
