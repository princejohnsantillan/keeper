<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;
use App\Models\Guardian;

final class UpsertOrganizationNoteAction
{
    public function __invoke(Child|Guardian $record, ?string $note): void
    {
        $normalizedNote = trim((string) $note);

        if ($normalizedNote === '') {
            $record->organizationNote()->delete();

            return;
        }

        $record->organizationNote()->updateOrCreate(
            [],
            ['note' => $normalizedNote],
        );
    }
}
