<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\CloseActivityCheckInAction as CloseActivityCheckInBusinessAction;
use App\Actions\GetCurrentKeeperAction;
use App\Filament\Notifications\AppNotification;
use App\Models\Activity;
use Filament\Actions\Action;

final class CloseActivityCheckInAction
{
    public static function make(?string $name = 'close_checkin'): Action
    {
        return Action::make($name)
            ->label('Close Check-In')
            ->icon('heroicon-o-lock-closed')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Close check-in')
            ->modalDescription(fn (Activity $record): string => "Close check-in for {$record->title}?")
            ->visible(fn (Activity $record): bool => app(GetCurrentKeeperAction::class)->__invoke()->isAdmin() && $record->isCheckInOpen())
            ->action(function (Activity $record, CloseActivityCheckInBusinessAction $closeActivityCheckInAction): void {
                $closeActivityCheckInAction($record);

                AppNotification::checkInClosed($record->title)->send();
            });
    }
}
