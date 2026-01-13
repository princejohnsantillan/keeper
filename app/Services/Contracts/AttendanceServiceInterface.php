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
     * @return array{success: bool, message: string, attendance: Attendance|null, child_name: string}
     */
    public function checkIn(Activity $activity, Gatepass $gatepass, Keeper $keeper): array;

    /**
     * Check out a child using a gatepass.
     *
     * @return array{success: bool, message: string, attendance: Attendance|null, child_name: string}
     */
    public function checkOut(Activity $activity, Gatepass $gatepass, Keeper $keeper): array;

    /**
     * Check if a child is currently checked in for an activity.
     */
    public function isCheckedIn(int $activityId, int $childId): bool;

    /**
     * Find an active attendance record (checked in but not checked out).
     */
    public function findActiveAttendance(int $activityId, int $childId): ?Attendance;

    /**
     * Check if a child has already been checked out for an activity.
     */
    public function isAlreadyCheckedOut(int $activityId, int $childId): bool;

    /**
     * Generate a unique attendee code.
     */
    public function generateUniqueAttendeeCode(): string;
}
