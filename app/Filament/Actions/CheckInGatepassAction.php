<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\GetCurrentKeeperAction;
use App\Actions\IsCheckedInAction;
use App\Models\Attendance;
use App\Models\Gatepass;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

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
            ->hidden(fn (Gatepass $record, IsCheckedInAction $isCheckedIn): bool => $isCheckedIn($record))
            ->action(function (Gatepass $record, GetCurrentKeeperAction $getCurrentKeeper): void {
                $childName = $record->child->full_name;

                $existingAttendance = Attendance::query()
                    ->where('activity_id', $record->activity_id)
                    ->where('child_id', $record->child_id)
                    ->whereNotNull('checked_in_at')
                    ->whereNull('checked_out_at')
                    ->exists();

                if ($existingAttendance) {
                    Notification::make()
                        ->title('Already checked in')
                        ->body("{$childName} is already checked in to this activity.")
                        ->warning()
                        ->send();

                    return;
                }

                $keeper = $getCurrentKeeper();

                Attendance::create([
                    'activity_id' => $record->activity_id,
                    'child_id' => $record->child_id,
                    'checkin_keeper_id' => $keeper->id,
                    'checkin_gatepass_id' => $record->id,
                    'checked_in_at' => now(),
                ]);

                Notification::make()
                    ->title('Checked in')
                    ->body("{$childName} has been checked in successfully.")
                    ->success()
                    ->send();
            });
    }
}
