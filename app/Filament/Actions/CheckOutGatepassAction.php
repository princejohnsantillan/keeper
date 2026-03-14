<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\GetCurrentKeeperAction;
use App\Filament\Notifications\AppNotification;
use App\Models\Gatepass;
use App\Services\Contracts\AttendanceServiceInterface;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class CheckOutGatepassAction
{
    public static function make(?string $name = 'check_out', string $label = 'Check Out'): Action
    {
        return Action::make($name)->label($label)
            ->icon(Heroicon::ArrowRightStartOnRectangle)
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Check Out Child')
            ->modalDescription(fn (Gatepass $record): string => "Are you sure you want to check out {$record->child->full_name} from {$record->activity->title}?")
            ->visible(fn (Gatepass $record, AttendanceServiceInterface $attendanceService): bool => $attendanceService->resolveGatepassActionState($record)['can_check_out'])
            ->action(function (
                Gatepass $record,
                GetCurrentKeeperAction $getCurrentKeeper,
                AttendanceServiceInterface $attendanceService,
            ): void {
                $keeper = $getCurrentKeeper();
                $result = $attendanceService->checkOut($record->activity, $record, $keeper);

                if (! $result['success']) {
                    match ($result['message']) {
                        'no_check_in_found' => AppNotification::noCheckInFound($result['child_name'])->send(),
                        default => AppNotification::error('Check-out failed.')->send(),
                    };

                    return;
                }

                AppNotification::checkedOut($result['child_name'])->send();
            });
    }
}
