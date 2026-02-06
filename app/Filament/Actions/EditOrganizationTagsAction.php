<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\SyncOrganizationTagsAction;
use App\Filament\Notifications\AppNotification;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Tag;
use Filament\Actions\Action;
use Filament\Forms\Components\TagsInput;

final class EditOrganizationTagsAction
{
    public static function make(?string $name = 'edit_tags', string $label = 'Tags'): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon('heroicon-o-tag')
            ->slideOver()
            ->modalHeading(fn (Child|Guardian $record): string => 'Tags for '.$record->full_name)
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn (Child|Guardian $record): array => [
                'tags' => $record->tags()->pluck('name')->toArray(),
            ])
            ->schema([
                self::tagsField()
                    ->columnSpanFull(),
            ])
            ->action(function (
                Child|Guardian $record,
                array $data,
                SyncOrganizationTagsAction $syncOrganizationTags,
            ): void {
                /** @var array<int, mixed> $tags */
                $tags = $data['tags'] ?? [];

                $syncOrganizationTags($record, $tags);

                AppNotification::tagsUpdated()->send();
            });
    }

    private static function tagsField(): TagsInput
    {
        return TagsInput::make('tags')
            ->label('Tags')
            ->suggestions(fn (): array => Tag::query()->pluck('name')->toArray())
            ->nestedRecursiveRules([
                'string',
                'max:255',
            ]);
    }
}
