<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Guardian;
use Illuminate\Support\Collection;

final class DetachGuardianFromChildrenAction
{
    /**
     * Soft delete a guardian. Relationships are preserved for reporting purposes.
     *
     * @param  Collection<int, int>|array<int, int>  $childIds
     */
    public function __invoke(Guardian $guardian, Collection|array $childIds): void
    {
        $guardian->delete();
    }
}
