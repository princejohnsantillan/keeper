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
            ->visible(fn (Gatepass $record, IsCheckedInAction $isCheckedIn): bool => $isCheckedIn($record))
            ->action(function (Gatepass $record, GetCurrentKeeperAction $getCurrentKeeper): void {
                $childName = $record->child->full_name;

                $attendance = Attendance::query()
                    ->where('activity_id', $record->activity_id)
                    ->where('child_id', $record->child_id)
                    ->whereNotNull('checked_in_at')
                    ->whereNull('checked_out_at')
                    ->first();

                if ($attendance === null) {
                    Notification::make()
                        ->title('No check-in found')
                        ->body("{$childName} has not been checked in to this activity.")
                        ->danger()
                        ->send();

                    return;
                }

                $keeper = $getCurrentKeeper();

                $attendance->update([
                    'checkout_keeper_id' => $keeper->id,
                    'checkout_gatepass_id' => $record->id,
                    'checked_out_at' => now(),
                ]);

                Notification::make()
                    ->title('Checked out')
                    ->body("{$childName} has been checked out successfully.")
                    ->success()
                    ->send();
            });
    }
}
