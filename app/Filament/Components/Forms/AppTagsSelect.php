<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use App\Models\Tag;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;

final class AppTagsSelect
{
    public static function tags(string $field = 'tags', ?string $label = null): Select
    {
        return Select::make($field)->label($label)
            ->multiple()
            ->searchable()
            ->getSearchResultsUsing(function (string $search): array {
                $tags = Tag::query()
                    ->where('name', 'ilike', "%{$search}%")
                    ->limit(50)
                    ->get();

                $options = $tags
                    ->mapWithKeys(fn (Tag $tag): array => [$tag->getKey() => $tag->name])
                    ->toArray();

                // Check if exact match exists
                $exactMatch = $tags->first(fn (Tag $tag): bool => strtolower($tag->name) === strtolower($search));

                // If no exact match, offer to create the tag
                if (! $exactMatch && trim($search) !== '') {
                    $newTag = Tag::findOrCreateFromString($search);
                    $options = [$newTag->getKey() => $newTag->name] + $options;
                }

                return $options;
            })
            ->getOptionLabelsUsing(function (array $values): array {
                return Tag::query()
                    ->whereIn('id', $values)
                    ->get()
                    ->mapWithKeys(fn (Tag $tag): array => [$tag->getKey() => $tag->name])
                    ->toArray();
            })
            ->options(function (): array {
                return Tag::query()
                    ->orderBy('name')
                    ->limit(50)
                    ->get()
                    ->mapWithKeys(fn (Tag $tag): array => [$tag->getKey() => $tag->name])
                    ->toArray();
            })
            ->afterStateHydrated(function (Select $component, ?Model $record): void {
                if (! $record) {
                    return;
                }

                $tagIds = $record->tags->pluck('id')->toArray();
                $component->state($tagIds);
            })
            ->dehydrated(false)
            ->saveRelationshipsUsing(function (?Model $record, ?array $state): void {
                if (! $record) {
                    return;
                }

                $state ??= [];

                $record->tags()->sync($state);
            });
    }
}
