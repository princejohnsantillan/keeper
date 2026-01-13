<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\GetCurrentKeeperAction;
use App\Filament\Notifications\AppNotification;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Gatepass;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
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
                    AppNotification::invalidGatepassCode()->send();

                    return;
                }

                $childName = $gatepass->child->full_name;

                $alreadyCheckedOut = Attendance::query()
                    ->where('activity_id', $activity->id)
                    ->where('child_id', $gatepass->child_id)
                    ->whereNotNull('checked_out_at')
                    ->exists();

                if ($alreadyCheckedOut) {
                    AppNotification::alreadyCheckedOut($childName)->send();

                    return;
                }

                $attendance = Attendance::query()
                    ->where('activity_id', $activity->id)
                    ->where('child_id', $gatepass->child_id)
                    ->whereNotNull('checked_in_at')
                    ->whereNull('checked_out_at')
                    ->first();

                if ($attendance === null) {
                    AppNotification::noCheckInFound($childName)->send();

                    return;
                }

                $keeper = $getCurrentKeeper();

                $attendance->update([
                    'checkout_keeper_id' => $keeper->id,
                    'checkout_gatepass_id' => $gatepass->id,
                    'checked_out_at' => now(),
                ]);

                AppNotification::checkedOut($childName)->send();
            });
    }

    private static function gatepassCodeInput(): TextInput
    {
        return TextInput::make('code')
            ->label('Gatepass Code')
            ->required();
    }
}
