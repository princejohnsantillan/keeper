<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Gatepass;
use App\Models\Keeper;
use App\Services\Contracts\AttendanceServiceInterface;

final class AttendanceService implements AttendanceServiceInterface
{
    /**
     * @return array{
     *     success: bool,
     *     message: 'checked_in'|'already_checked_in'|'activity_not_published'|'activity_ended'|'checkin_not_open'|'checkin_closed',
     *     attendance: Attendance|null,
     *     child_name: string
     * }
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

        if (! $this->isActivityPublished($activity)) {
            return [
                'success' => false,
                'message' => 'activity_not_published',
                'attendance' => null,
                'child_name' => $childName,
            ];
        }

        if ($this->hasActivityEnded($activity)) {
            return [
                'success' => false,
                'message' => 'activity_ended',
                'attendance' => null,
                'child_name' => $childName,
            ];
        }

        if ($activity->hasCheckInClosed()) {
            return [
                'success' => false,
                'message' => 'checkin_closed',
                'attendance' => null,
                'child_name' => $childName,
            ];
        }

        if (! $activity->hasCheckInOpened()) {
            return [
                'success' => false,
                'message' => 'checkin_not_open',
                'attendance' => null,
                'child_name' => $childName,
            ];
        }

        $attendance = Attendance::query()->create([
            'activity_id' => $activity->id,
            'organization_id' => $activity->organization_id,
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
     * @return array{
     *     success: bool,
     *     message: 'checked_out'|'no_check_in_found',
     *     attendance: Attendance|null,
     *     child_name: string
     * }
     */
    public function checkOut(Activity $activity, Gatepass $gatepass, Keeper $keeper): array
    {
        $childName = $gatepass->child->full_name;

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

    public function isCheckedIn(string $activityId, string $childId): bool
    {
        return $this->findActiveAttendance($activityId, $childId) !== null;
    }

    public function findActiveAttendance(string $activityId, string $childId): ?Attendance
    {
        return Attendance::query()
            ->where('activity_id', $activityId)
            ->where('child_id', $childId)
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->first();
    }

    public function findLatestAttendance(string $activityId, string $childId): ?Attendance
    {
        return Attendance::query()
            ->where('activity_id', $activityId)
            ->where('child_id', $childId)
            ->latest('checked_in_at')
            ->first();
    }

    public function resolveGatepassActionState(Gatepass $gatepass): array
    {
        $activity = $gatepass->activity;
        $activeAttendance = $this->findActiveAttendance($gatepass->activity_id, $gatepass->child_id);
        $latestAttendance = $this->findLatestAttendance($gatepass->activity_id, $gatepass->child_id);

        $status = match (true) {
            $activeAttendance !== null => 'checked_in',
            $latestAttendance?->checked_out_at !== null => 'checked_out',
            default => 'not_checked_in',
        };

        if ($activity === null) {
            return [
                'status' => $status,
                'can_check_in' => false,
                'can_check_out' => false,
                'reason' => 'activity_unavailable',
            ];
        }

        $isPublished = $this->isActivityPublished($activity);
        $hasEnded = $this->hasActivityEnded($activity);

        return [
            'status' => $status,
            'can_check_in' => $isPublished && (! $hasEnded) && $activity->isCheckInOpen() && $activeAttendance === null,
            'can_check_out' => $isPublished && $activeAttendance !== null,
            'reason' => match (true) {
                $activeAttendance !== null => null,
                ! $isPublished => 'not_published',
                $hasEnded && $activeAttendance === null => 'event_ended',
                $activity->hasCheckInClosed() => 'checkin_closed',
                ! $activity->hasCheckInOpened() => 'checkin_not_open',
                default => null,
            },
        ];
    }

    private function isActivityPublished(?Activity $activity): bool
    {
        return $activity?->publish_at !== null && $activity->publish_at->lessThanOrEqualTo(now());
    }

    private function hasActivityEnded(?Activity $activity): bool
    {
        return $activity?->ends_at !== null && $activity->ends_at->isPast();
    }
}
