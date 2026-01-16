<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Notifications\AppNotification;
use App\Models\Term;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class DeprecateTermAction
{
    public static function make(?string $name = 'deprecate'): Action
    {
        return Action::make($name)
            ->label('Deprecate')
            ->icon(Heroicon::OutlinedArchiveBoxXMark)
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon(Heroicon::OutlinedArchiveBoxXMark)
            ->modalHeading('Deprecate Terms & Conditions')
            ->modalDescription('Are you sure you want to deprecate these terms and conditions? This action is irreversible and this item will no longer be available for selection when creating activities.')
            ->modalSubmitActionLabel('Yes, deprecate')
            ->hidden(fn (Term $record): bool => $record->isDeprecated())
            ->action(function (Term $record): void {
                $record->update(['deprecated_at' => now()]);

                AppNotification::deprecated()->send();
            });
    }
}
