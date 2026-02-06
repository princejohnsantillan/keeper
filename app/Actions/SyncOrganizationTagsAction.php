<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\Tag;

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
            ->map(fn (string $tagName): string => trim($tagName))
            ->filter(fn (string $tagName): bool => $tagName !== '')
            ->unique(fn (string $tagName): string => strtolower($tagName))
            ->values()
            ->all();

        /** @var list<string> $tagIds */
        $tagIds = collect($normalizedTagNames)
            ->map(fn (string $tagName): string => Tag::findOrCreateFromString($tagName)->id)
            ->all();

        /** @var list<string> $currentOrganizationTagIds */
        $currentOrganizationTagIds = $record->tags()
            ->pluck('tags.id')
            ->all();

        /** @var list<string> $tagIdsToDetach */
        $tagIdsToDetach = array_values(array_diff($currentOrganizationTagIds, $tagIds));

        if ($tagIdsToDetach !== []) {
            $record->tags()->detach($tagIdsToDetach);
        }

        if ($tagIds !== []) {
            $record->tags()->syncWithoutDetaching($tagIds);
        }
    }
}
