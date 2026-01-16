<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Notifications\AppNotification;
use App\Models\Term;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class ArchiveTermAction
{
    public static function make(?string $name = 'archive'): Action
    {
        return Action::make($name)
            ->label('Archive')
            ->icon(Heroicon::OutlinedArchiveBoxXMark)
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon(Heroicon::OutlinedArchiveBoxXMark)
            ->modalHeading('Archive Terms & Conditions')
            ->modalDescription('Are you sure you want to archive these terms and conditions? This action is irreversible and this item will no longer be available for selection when creating activities.')
            ->modalSubmitActionLabel('Yes, archive')
            ->hidden(fn (Term $record): bool => $record->isArchived())
            ->action(function (Term $record): void {
                $record->update(['archived_at' => now()]);

                AppNotification::archived()->send();
            });
    }
}
