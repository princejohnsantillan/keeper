<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use App\Actions\SyncOrganizationTagsAction;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\OrganizationTag;
use Filament\Forms\Components\TagsInput;
use Illuminate\Database\Eloquent\Model;

final class AppTagsInput
{
    public static function tags(string $field = 'tags', ?string $label = null): TagsInput
    {
        return TagsInput::make($field)->label($label)
            ->suggestions(fn (): array => OrganizationTag::query()
                ->distinct()
                ->orderBy('name')
                ->pluck('name')
                ->all())
            ->afterStateHydrated(function (TagsInput $component, ?Model $record): void {
                if (! $record) {
                    return;
                }

                if (! method_exists($record, 'organizationTags')) {
                    return;
                }

                $component->state($record->organizationTags()->pluck('name')->toArray());
            })
            ->dehydrated(false)
            ->saveRelationshipsUsing(function (?Model $record, ?array $state): void {
                if (! $record) {
                    return;
                }

                if (! method_exists($record, 'organizationTags')) {
                    return;
                }

                if ($record instanceof Child || $record instanceof Guardian) {
                    $syncOrganizationTags = app(SyncOrganizationTagsAction::class);
                    $syncOrganizationTags($record, $state ?? []);
                }
            });
    }
}
