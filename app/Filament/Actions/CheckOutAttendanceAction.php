<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\GetCurrentKeeperAction;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Gatepass;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

final class CheckOutAttendanceAction
{
    public static function make(Activity $activity, ?string $name = 'check_out', string $label = 'Check Out'): Action
    {
        return Action::make($name)->label($label)
            ->icon(Heroicon::ArrowRightStartOnRectangle)
            ->color('warning')
            ->schema([
                self::gatepassCodeInput()
                    ->autofocus(),
            ])
            ->action(function (array $data, GetCurrentKeeperAction $getCurrentKeeper) use ($activity): void {
                $gatepass = Gatepass::query()
                    ->with('child')
                    ->where('code', $data['code'])
                    ->where('activity_id', $activity->id)
                    ->first();

                if ($gatepass === null) {
                    Notification::make()
                        ->title('Invalid code')
                        ->body('The gatepass code does not match this activity.')
                        ->danger()
                        ->send();

                    return;
                }

                $childName = $gatepass->child->full_name;

                $alreadyCheckedOut = Attendance::query()
                    ->where('activity_id', $activity->id)
                    ->where('child_id', $gatepass->child_id)
                    ->whereNotNull('checked_out_at')
                    ->exists();

                if ($alreadyCheckedOut) {
                    Notification::make()
                        ->title('Already checked out')
                        ->body("{$childName} has already been checked out of this activity.")
                        ->warning()
                        ->send();

                    return;
                }

                $attendance = Attendance::query()
                    ->where('activity_id', $activity->id)
                    ->where('child_id', $gatepass->child_id)
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
                    'checkout_gatepass_id' => $gatepass->id,
                    'checked_out_at' => now(),
                ]);

                Notification::make()
                    ->title('Checked out')
                    ->body("{$childName} has been checked out successfully.")
                    ->success()
                    ->send();
            });
    }

    private static function gatepassCodeInput(): TextInput
    {
        return TextInput::make('code')
            ->label('Gatepass Code')
            ->required();
    }
}
