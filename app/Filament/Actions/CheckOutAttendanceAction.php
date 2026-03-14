<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Actions\GetCurrentKeeperAction;
use App\Filament\Notifications\AppNotification;
use App\Models\Activity;
use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\GatepassServiceInterface;
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
            ->action(function (
                array $data,
                GetCurrentKeeperAction $getCurrentKeeper,
                AttendanceServiceInterface $attendanceService,
                GatepassServiceInterface $gatepassService,
            ) use ($activity): void {
                $gatepass = $gatepassService->findByCodeAndActivity($data['code'], $activity->id);

                if ($gatepass === null) {
                    AppNotification::invalidGatepassCode()->send();

                    return;
                }

                $keeper = $getCurrentKeeper();
                $result = $attendanceService->checkOut($activity, $gatepass, $keeper);

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

    private static function gatepassCodeInput(): TextInput
    {
        return TextInput::make('code')
            ->label('Gatepass Code')
            ->required();
    }
}
