<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Gatepass;
use App\Models\Keeper;

interface AttendanceServiceInterface
{
    /**
     * Check in a child using a gatepass.
     *
     * @return array{
     *     success: bool,
     *     message: 'checked_in'|'already_checked_in'|'activity_not_published'|'activity_ended',
     *     attendance: Attendance|null,
     *     child_name: string
     * }
     */
    public function checkIn(Activity $activity, Gatepass $gatepass, Keeper $keeper): array;

    /**
     * Check out a child using a gatepass.
     *
     * @return array{
     *     success: bool,
     *     message: 'checked_out'|'no_check_in_found',
     *     attendance: Attendance|null,
     *     child_name: string
     * }
     */
    public function checkOut(Activity $activity, Gatepass $gatepass, Keeper $keeper): array;

    /**
     * Check if a child is currently checked in for an activity.
     */
    public function isCheckedIn(string $activityId, string $childId): bool;

    /**
     * Find an active attendance record (checked in but not checked out).
     */
    public function findActiveAttendance(string $activityId, string $childId): ?Attendance;

    /**
     * Find the most recent attendance record for an activity and child.
     */
    public function findLatestAttendance(string $activityId, string $childId): ?Attendance;

    /**
     * @return array{
     *     status: 'not_checked_in'|'checked_in'|'checked_out',
     *     can_check_in: bool,
     *     can_check_out: bool,
     *     reason: null|'not_published'|'event_ended'
     * }
     */
    public function resolveGatepassActionState(Gatepass $gatepass): array;
}
