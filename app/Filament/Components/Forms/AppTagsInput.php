<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use App\Actions\SyncOrganizationTagsAction;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Tag;
use Filament\Forms\Components\TagsInput;
use Illuminate\Database\Eloquent\Model;

final class AppTagsInput
{
    public static function tags(string $field = 'tags', ?string $label = null): TagsInput
    {
        return TagsInput::make($field)->label($label)
            ->suggestions(fn (): array => Tag::pluck('name')->toArray())
            ->afterStateHydrated(function (TagsInput $component, ?Model $record): void {
                if (! $record) {
                    return;
                }

                if (! method_exists($record, 'tags')) {
                    return;
                }

                $component->state($record->tags()->pluck('name')->toArray());
            })
            ->dehydrated(false)
            ->saveRelationshipsUsing(function (?Model $record, ?array $state): void {
                if (! $record) {
                    return;
                }

                if (! method_exists($record, 'tags')) {
                    return;
                }

                if ($record instanceof Child || $record instanceof Guardian) {
                    $syncOrganizationTags = app(SyncOrganizationTagsAction::class);
                    $syncOrganizationTags($record, $state ?? []);

                    return;
                }

                $tagIds = collect($state ?? [])
                    ->map(fn (string $name): string => Tag::findOrCreateFromString($name)->id)
                    ->toArray();

                $record->tags()->sync($tagIds);
            });
    }
}
