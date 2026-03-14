<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\GetCurrentKeeperAction;
use App\Filament\Notifications\AppNotification;
use App\Models\Gatepass;
use App\Services\Contracts\AttendanceServiceInterface;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

final class CheckInGatepassAction
{
    public static function make(?string $name = 'check_in', string $label = 'Check In'): Action
    {
        return Action::make($name)->label($label)
            ->icon(Heroicon::ArrowRightEndOnRectangle)
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Check In Child')
            ->modalDescription(fn (Gatepass $record): string => "Are you sure you want to check in {$record->child->full_name} for {$record->activity->title}?")
            ->visible(fn (Gatepass $record, AttendanceServiceInterface $attendanceService): bool => $attendanceService->resolveGatepassActionState($record)['can_check_in'])
            ->action(function (
                Gatepass $record,
                GetCurrentKeeperAction $getCurrentKeeper,
                AttendanceServiceInterface $attendanceService,
                Component $livewire,
            ): void {
                $keeper = $getCurrentKeeper();
                $result = $attendanceService->checkIn($record->activity, $record, $keeper);

                if (! $result['success']) {
                    match ($result['message']) {
                        'already_checked_in' => AppNotification::alreadyCheckedIn($result['child_name'])->send(),
                        'activity_not_published' => AppNotification::activityNotPublished($record->activity->title)->send(),
                        'activity_ended' => AppNotification::activityEnded($record->activity->title)->send(),
                        default => AppNotification::error('Check-in failed.')->send(),
                    };

                    return;
                }

                AppNotification::checkedIn($result['child_name'])->send();

                $printUrl = route('filament.keeper.attendance.print', $result['attendance']);
                $livewire->dispatch('open-print-sticker', url: $printUrl);
            });
    }
}
