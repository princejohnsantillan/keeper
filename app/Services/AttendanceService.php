<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Gatepass;
use App\Models\Keeper;
use App\ReadableCode;
use App\Services\Contracts\AttendanceServiceInterface;

final class AttendanceService implements AttendanceServiceInterface
{
    /**
     * @return array{success: bool, message: string, attendance: Attendance|null, child_name: string}
     */
    public function checkIn(Activity $activity, Gatepass $gatepass, Keeper $keeper): array
    {
        $childName = $gatepass->child->full_name;

        if ($this->isCheckedIn($activity->id, $gatepass->child_id)) {
            return [
                'success' => false,
                'message' => 'already_checked_in',
                'attendance' => null,
                'child_name' => $childName,
            ];
        }

        $attendeeCode = $this->generateUniqueAttendeeCode();

        $attendance = Attendance::query()->create([
            'attendee_code' => $attendeeCode,
            'activity_id' => $activity->id,
            'child_id' => $gatepass->child_id,
            'checkin_keeper_id' => $keeper->id,
            'checkin_gatepass_id' => $gatepass->id,
            'checked_in_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'checked_in',
            'attendance' => $attendance,
            'child_name' => $childName,
        ];
    }

    /**
     * @return array{success: bool, message: string, attendance: Attendance|null, child_name: string}
     */
    public function checkOut(Activity $activity, Gatepass $gatepass, Keeper $keeper): array
    {
        $childName = $gatepass->child->full_name;

        if ($this->isAlreadyCheckedOut($activity->id, $gatepass->child_id)) {
            return [
                'success' => false,
                'message' => 'already_checked_out',
                'attendance' => null,
                'child_name' => $childName,
            ];
        }

        $attendance = $this->findActiveAttendance($activity->id, $gatepass->child_id);

        if ($attendance === null) {
            return [
                'success' => false,
                'message' => 'no_check_in_found',
                'attendance' => null,
                'child_name' => $childName,
            ];
        }

        $attendance->update([
            'checkout_keeper_id' => $keeper->id,
            'checkout_gatepass_id' => $gatepass->id,
            'checked_out_at' => now(),
        ]);

        return [
            'success' => true,
            'message' => 'checked_out',
            'attendance' => $attendance,
            'child_name' => $childName,
        ];
    }

    public function isCheckedIn(int $activityId, int $childId): bool
    {
        return $this->findActiveAttendance($activityId, $childId) !== null;
    }

    public function findActiveAttendance(int $activityId, int $childId): ?Attendance
    {
        return Attendance::query()
            ->where('activity_id', $activityId)
            ->where('child_id', $childId)
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->first();
    }

    public function isAlreadyCheckedOut(int $activityId, int $childId): bool
    {
        return Attendance::query()
            ->where('activity_id', $activityId)
            ->where('child_id', $childId)
            ->whereNotNull('checked_out_at')
            ->exists();
    }

    public function generateUniqueAttendeeCode(): string
    {
        do {
            $attendeeCode = ReadableCode::generate();
        } while (Attendance::query()->where('attendee_code', $attendeeCode)->exists());

        return $attendeeCode;
    }
}
