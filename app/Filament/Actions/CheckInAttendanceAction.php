<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\GetCurrentKeeperAction;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Gatepass;
use App\ReadableCode;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

final class CheckInAttendanceAction
{
    public static function make(Activity $activity, ?string $name = 'check_in', string $label = 'Check In'): Action
    {
        return Action::make($name)->label($label)
            ->icon(Heroicon::ArrowRightEndOnRectangle)
            ->color('success')
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

                $existingAttendance = Attendance::query()
                    ->where('activity_id', $activity->id)
                    ->where('child_id', $gatepass->child_id)
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

                do {
                    $attendeeCode = ReadableCode::generate();
                } while (Attendance::query()->where('attendee_code', $attendeeCode)->exists());

                Attendance::create([
                    'attendee_code' => $attendeeCode,
                    'activity_id' => $activity->id,
                    'child_id' => $gatepass->child_id,
                    'checkin_keeper_id' => $keeper->id,
                    'checkin_gatepass_id' => $gatepass->id,
                    'checked_in_at' => now(),
                ]);

                Notification::make()
                    ->title('Checked in')
                    ->body("{$childName} has been checked in successfully.")
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
