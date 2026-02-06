<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;
use App\Models\Guardian;

final class SyncOrganizationTagsAction
{
    /**
     * @param  array<int, mixed>  $tagNames
     */
    public function __invoke(Child|Guardian $record, array $tagNames): void
    {
        /** @var list<string> $normalizedTagNames */
        $normalizedTagNames = collect($tagNames)
            ->filter(fn (mixed $tagName): bool => is_string($tagName))
            ->map(fn (string $tagName): string => strtolower(trim($tagName)))
            ->filter(fn (string $tagName): bool => $tagName !== '')
            ->unique()
            ->values()
            ->all();

        if ($normalizedTagNames === []) {
            $record->organizationTags()->delete();

            return;
        }

        $record->organizationTags()
            ->whereNotIn('name', $normalizedTagNames)
            ->delete();

        foreach ($normalizedTagNames as $normalizedTagName) {
            $record->organizationTags()->firstOrCreate([
                'name' => $normalizedTagName,
            ]);
        }
    }
}
