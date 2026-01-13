<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;

final class SyncChildGuardiansAction
{
    /**
     * Sync the guardians of a child with the given sync data.
     *
     * @param  array<int, array{relationship: string}>  $syncData  Guardian IDs mapped to pivot data
     */
    public function __invoke(Child $child, array $syncData): void
    {
        $child->guardians()->sync($syncData);
    }
}
