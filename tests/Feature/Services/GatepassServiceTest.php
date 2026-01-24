<?php

declare(strict_types=1);

use App\Mail\GatepassCreated;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\TermAcceptance;
use App\Models\User;
use App\Services\Contracts\GatepassServiceInterface;
use Illuminate\Support\Facades\Mail;

it('creates a gatepass for a child attending an activity', function () {
    Mail::fake();

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
    Mail::fake();

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
    Mail::fake();

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

it('sends email to guardian when gatepass is created', function () {
    Mail::fake();

    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();
    $user = User::factory()->create(['guardian_id' => $guardian->id]);

    $gatepass = $service->create($activity, $child, $guardian);

    Mail::assertQueued(GatepassCreated::class, function (GatepassCreated $mail) use ($gatepass, $user): bool {
        return $mail->gatepass->id === $gatepass->id
            && $mail->hasTo($user->email);
    });
});

it('does not send email when guardian has no user', function () {
    Mail::fake();

    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $service->create($activity, $child, $guardian);

    Mail::assertNothingQueued();
});

it('finds a gatepass by code and activity', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'ABCDE',
    ]);

    $found = $service->findByCodeAndActivity('ABCDE', $activity->id);

    expect($found)
        ->not->toBeNull()
        ->id->toBe($gatepass->id)
        ->code->toBe('ABCDE');
});

it('returns null when gatepass code does not exist', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();

    $found = $service->findByCodeAndActivity('NOTEX', $activity->id);

    expect($found)->toBeNull();
});

it('returns null when code exists but for different activity', function () {
    $service = app(GatepassServiceInterface::class);
    $activity1 = Activity::factory()->create();
    $activity2 = Activity::factory()->create();

    Gatepass::factory()->create([
        'activity_id' => $activity1->id,
        'code' => 'ABCDE',
    ]);

    $found = $service->findByCodeAndActivity('ABCDE', $activity2->id);

    expect($found)->toBeNull();
});

it('loads child relationship when finding by code and activity', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create(['first_name' => 'TestChild']);

    Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'code' => 'ABCDE',
    ]);

    $found = $service->findByCodeAndActivity('ABCDE', $activity->id);

    expect($found)
        ->not->toBeNull()
        ->child->not->toBeNull()
        ->child->first_name->toBe('TestChild');
});

it('finds an existing gatepass for activity, child, and guardian', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
    ]);

    $found = $service->findExisting($activity, $child, $guardian);

    expect($found)
        ->not->toBeNull()
        ->id->toBe($gatepass->id);
});

it('returns null when no existing gatepass for combination', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $found = $service->findExisting($activity, $child, $guardian);

    expect($found)->toBeNull();
});

it('returns null when gatepass exists but for different child', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child1 = Child::factory()->create();
    $child2 = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child1->id,
        'guardian_id' => $guardian->id,
    ]);

    $found = $service->findExisting($activity, $child2, $guardian);

    expect($found)->toBeNull();
});

it('returns null when gatepass exists but for different guardian', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create();
    $child = Child::factory()->create();
    $guardian1 = Guardian::factory()->create();
    $guardian2 = Guardian::factory()->create();

    Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian1->id,
    ]);

    $found = $service->findExisting($activity, $child, $guardian2);

    expect($found)->toBeNull();
});
