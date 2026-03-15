<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\GetCurrentKeeperAction;
use App\Actions\OpenActivityCheckInAction as OpenActivityCheckInBusinessAction;
use App\Filament\Notifications\AppNotification;
use App\Models\Activity;
use Filament\Actions\Action;

final class OpenActivityCheckInAction
{
    public static function make(?string $name = 'open_checkin'): Action
    {
        return Action::make($name)
            ->label(fn (Activity $record): string => $record->hasCheckInClosed() ? 'Reopen Check-In' : 'Open Check-In')
            ->icon('heroicon-o-lock-open')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(fn (Activity $record): string => $record->hasCheckInClosed() ? 'Reopen check-in' : 'Open check-in')
            ->modalDescription(fn (Activity $record): string => $record->hasCheckInClosed()
                ? "Reopen check-in for {$record->title}?"
                : "Open check-in for {$record->title}?")
            ->visible(fn (Activity $record): bool => app(GetCurrentKeeperAction::class)->__invoke()->isAdmin() && (! $record->isCheckInOpen()))
            ->action(function (Activity $record, OpenActivityCheckInBusinessAction $openActivityCheckInAction): void {
                $hasPreviouslyClosed = $record->hasCheckInClosed();

                $openActivityCheckInAction($record);

                if ($hasPreviouslyClosed) {
                    AppNotification::checkInReopened($record->title)->send();

                    return;
                }

                AppNotification::checkInOpened($record->title)->send();
            });
    }
}
