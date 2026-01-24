<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Term;

final class ArchiveTermAction
{
    /**
     * Archive a term.
     */
    public function __invoke(Term $term): void
    {
        $term->update(['archived_at' => now()]);
    }
}
