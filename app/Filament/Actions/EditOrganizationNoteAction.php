<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\UpsertOrganizationNoteAction;
use App\Filament\Notifications\AppNotification;
use App\Models\Child;
use App\Models\Guardian;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;

final class EditOrganizationNoteAction
{
    public static function make(?string $name = 'edit_note', string $label = 'Note'): Action
    {
        return Action::make($name)
            ->label($label)
            ->icon('heroicon-o-document-text')
            ->slideOver()
            ->modalHeading(fn (Child|Guardian $record): string => 'Note for '.$record->full_name)
            ->modalSubmitActionLabel('Save')
            ->fillForm(fn (Child|Guardian $record): array => [
                'note' => $record->organizationNote()->value('note'),
            ])
            ->schema([
                self::noteField()
                    ->columnSpanFull(),
            ])
            ->action(function (
                Child|Guardian $record,
                array $data,
                UpsertOrganizationNoteAction $upsertOrganizationNote,
            ): void {
                $upsertOrganizationNote($record, $data['note'] ?? null);

                AppNotification::noteUpdated()->send();
            });
    }

    private static function noteField(): Textarea
    {
        return Textarea::make('note')
            ->label('Note')
            ->rules(['nullable', 'string'])
            ->rows(4);
    }
}
