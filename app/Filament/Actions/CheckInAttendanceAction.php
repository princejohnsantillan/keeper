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
                $result = $attendanceService->checkIn($activity, $gatepass, $keeper);

                if (! $result['success']) {
                    match ($result['message']) {
                        'already_checked_in' => AppNotification::alreadyCheckedIn($result['child_name'])->send(),
                        'activity_not_published' => AppNotification::activityNotPublished($activity->title)->send(),
                        'activity_ended' => AppNotification::activityEnded($activity->title)->send(),
                        default => AppNotification::error('Check-in failed.')->send(),
                    };

                    return;
                }

                AppNotification::checkedIn($result['child_name'])->send();
            });
    }

    private static function gatepassCodeInput(): TextInput
    {
        return TextInput::make('code')
            ->label('Gatepass Code')
            ->required();
    }
}
