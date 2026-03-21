<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Models\Gatepass;
use App\Services\Contracts\AttendanceServiceInterface;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

final class AttendanceStatusAction
{
    public static function make(?string $name = 'attendance_status', string $label = 'Status'): Action
    {
        return Action::make($name)->label($label)
            ->icon(Heroicon::InformationCircle)
            ->disabled()
            ->label(function (Gatepass $record, AttendanceServiceInterface $attendanceService): string {
                $attendanceState = $attendanceService->resolveGatepassActionState($record);

                return match (true) {
                    $attendanceState['reason'] === 'activity_unavailable' => 'Activity unavailable',
                    $attendanceState['reason'] === 'not_published' => 'Activity not published',
                    $attendanceState['reason'] === 'event_ended' => 'Activity ended',
                    $attendanceState['reason'] === 'checkin_closed' => 'Check-in closed',
                    $attendanceState['reason'] === 'checkin_not_open' => 'Check-in not yet open',
                    $attendanceState['status'] === 'checked_out' => 'Checked out',
                    default => '—',
                };
            })
            ->color(function (Gatepass $record, AttendanceServiceInterface $attendanceService): string {
                $attendanceState = $attendanceService->resolveGatepassActionState($record);

                return match ($attendanceState['reason']) {
                    'activity_unavailable', 'not_published', 'checkin_not_open' => 'danger',
                    'event_ended', 'checkin_closed' => 'warning',
                    default => 'gray',
                };
            })
            ->visible(function (Gatepass $record, AttendanceServiceInterface $attendanceService): bool {
                $attendanceState = $attendanceService->resolveGatepassActionState($record);

                return ! $attendanceState['can_check_in'] && ! $attendanceState['can_check_out'];
            });
    }
}
