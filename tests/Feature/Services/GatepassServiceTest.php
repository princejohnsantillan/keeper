<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\TermAcceptance;
use App\Services\Contracts\GatepassServiceInterface;

it('creates a gatepass for a child attending an activity', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = $service->create($activity, $child, $guardian);

    expect($gatepass)
        ->toBeInstanceOf(Gatepass::class)
        ->activity_id->toBe($activity->id)
        ->child_id->toBe($child->id)
        ->guardian_id->toBe($guardian->id)
        ->code->toBeString()
        ->code->toHaveLength(5)
        ->term_acceptance_id->toBeNull();

    $this->assertDatabaseHas(Gatepass::class, [
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
    ]);
});

it('creates a gatepass with term acceptance', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();
    $termAcceptance = TermAcceptance::factory()->create([
        'guardian_id' => $guardian->id,
    ]);

    $gatepass = $service->create($activity, $child, $guardian, $termAcceptance);

    expect($gatepass)
        ->toBeInstanceOf(Gatepass::class)
        ->term_acceptance_id->toBe($termAcceptance->id);
});

it('generates unique codes for an activity', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();

    $codes = [];
    for ($i = 0; $i < 10; $i++) {
        $code = $service->generateUniqueCode($activity);
        expect($code)->toBeString()->toHaveLength(5);
        $codes[] = $code;
    }

    expect(count(array_unique($codes)))->toBe(10);
});

it('generates codes that are unique within the activity', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass1 = $service->create($activity, $child, $guardian);
    $gatepass2 = $service->create($activity, Child::factory()->create(), $guardian);

    expect($gatepass1->code)->not->toBe($gatepass2->code);
});

it('allows same code for different activities', function () {
    $activity1 = Activity::factory()->create();
    $activity2 = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass1 = Gatepass::factory()->create([
        'activity_id' => $activity1->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'TEST1',
    ]);

    $gatepass2 = Gatepass::factory()->create([
        'activity_id' => $activity2->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'TEST1',
    ]);

    expect($gatepass1->code)->toBe($gatepass2->code);
    expect($gatepass1->activity_id)->not->toBe($gatepass2->activity_id);
});
