<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Keeper;
use App\Services\Contracts\AttendanceServiceInterface;

it('checks in a child successfully', function () {
    $service = app(AttendanceServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
    ]);
    $keeper = Keeper::factory()->create();

    $result = $service->checkIn($activity, $gatepass, $keeper);

    expect($result)
        ->success->toBeTrue()
        ->message->toBe('checked_in')
        ->child_name->toBe($child->full_name)
        ->attendance->toBeInstanceOf(Attendance::class);

    expect($result['attendance'])
        ->activity_id->toBe($activity->id)
        ->child_id->toBe($child->id)
        ->checkin_keeper_id->toBe($keeper->id)
        ->checkin_gatepass_id->toBe($gatepass->id)
        ->checked_in_at->not->toBeNull()
        ->checked_out_at->toBeNull();

    $this->assertDatabaseHas(Attendance::class, [
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checkin_keeper_id' => $keeper->id,
    ]);
});

it('fails to check in when child is already checked in', function () {
    $service = app(AttendanceServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
    ]);
    $keeper = Keeper::factory()->create();

    Attendance::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checked_in_at' => now(),
        'checked_out_at' => null,
    ]);

    $result = $service->checkIn($activity, $gatepass, $keeper);

    expect($result)
        ->success->toBeFalse()
        ->message->toBe('already_checked_in')
        ->attendance->toBeNull()
        ->child_name->toBe($child->full_name);
});

it('checks out a child successfully', function () {
    $service = app(AttendanceServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
    ]);
    $keeper = Keeper::factory()->create();

    $attendance = Attendance::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checked_in_at' => now()->subHour(),
        'checked_out_at' => null,
    ]);

    $result = $service->checkOut($activity, $gatepass, $keeper);

    expect($result)
        ->success->toBeTrue()
        ->message->toBe('checked_out')
        ->child_name->toBe($child->full_name)
        ->attendance->toBeInstanceOf(Attendance::class);

    expect($result['attendance'])
        ->checkout_keeper_id->toBe($keeper->id)
        ->checkout_gatepass_id->toBe($gatepass->id)
        ->checked_out_at->not->toBeNull();

    $this->assertDatabaseHas(Attendance::class, [
        'id' => $attendance->id,
        'checkout_keeper_id' => $keeper->id,
        'checkout_gatepass_id' => $gatepass->id,
    ]);
});

it('fails to check out when child is already checked out', function () {
    $service = app(AttendanceServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
    ]);
    $keeper = Keeper::factory()->create();

    Attendance::factory()->checkedOut()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
    ]);

    $result = $service->checkOut($activity, $gatepass, $keeper);

    expect($result)
        ->success->toBeFalse()
        ->message->toBe('already_checked_out')
        ->attendance->toBeNull()
        ->child_name->toBe($child->full_name);
});

it('fails to check out when no check-in found', function () {
    $service = app(AttendanceServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
    ]);
    $keeper = Keeper::factory()->create();

    $result = $service->checkOut($activity, $gatepass, $keeper);

    expect($result)
        ->success->toBeFalse()
        ->message->toBe('no_check_in_found')
        ->attendance->toBeNull()
        ->child_name->toBe($child->full_name);
});

it('correctly identifies when a child is checked in', function () {
    $service = app(AttendanceServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();

    expect($service->isCheckedIn($activity->id, $child->id))->toBeFalse();

    Attendance::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checked_in_at' => now(),
        'checked_out_at' => null,
    ]);

    expect($service->isCheckedIn($activity->id, $child->id))->toBeTrue();
});

it('correctly identifies when a child is already checked out', function () {
    $service = app(AttendanceServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();

    expect($service->isAlreadyCheckedOut($activity->id, $child->id))->toBeFalse();

    Attendance::factory()->checkedOut()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
    ]);

    expect($service->isAlreadyCheckedOut($activity->id, $child->id))->toBeTrue();
});

it('finds active attendance correctly', function () {
    $service = app(AttendanceServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();

    expect($service->findActiveAttendance($activity->id, $child->id))->toBeNull();

    $attendance = Attendance::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'checked_in_at' => now(),
        'checked_out_at' => null,
    ]);

    $found = $service->findActiveAttendance($activity->id, $child->id);

    expect($found)
        ->not->toBeNull()
        ->id->toBe($attendance->id);
});

it('does not find checked out attendance as active', function () {
    $service = app(AttendanceServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();

    Attendance::factory()->checkedOut()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
    ]);

    expect($service->findActiveAttendance($activity->id, $child->id))->toBeNull();
});
