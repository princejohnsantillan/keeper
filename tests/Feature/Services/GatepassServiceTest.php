<?php

declare(strict_types=1);

use App\Mail\GatepassCreated;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\Organization;
use App\Models\TermAcceptance;
use App\Models\User;
use App\Services\Contracts\GatepassServiceInterface;
use Illuminate\Database\QueryException;
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

it('generates unique codes within an organization', function () {
    $service = app(GatepassServiceInterface::class);
    $organization = Organization::factory()->create();

    $codes = [];
    for ($i = 0; $i < 10; $i++) {
        $code = $service->generateUniqueCode($organization);
        expect($code)->toBeString()->toHaveLength(5);
        $codes[] = $code;
    }

    expect(count(array_unique($codes)))->toBe(10);
});

it('generates unique codes across activities within same organization', function () {
    Mail::fake();

    $service = app(GatepassServiceInterface::class);
    $organization = Organization::factory()->create();
    $activity1 = Activity::factory()->create(['organization_id' => $organization->id]);
    $activity2 = Activity::factory()->create(['organization_id' => $organization->id]);
    $guardian = Guardian::factory()->create();

    $gatepass1 = $service->create($activity1, Child::factory()->create(), $guardian);
    $gatepass2 = $service->create($activity2, Child::factory()->create(), $guardian);

    expect($gatepass1->code)->not->toBe($gatepass2->code);
});

it('enforces unique code constraint within organization', function () {
    $organization = Organization::factory()->create();
    Gatepass::factory()->create([
        'organization_id' => $organization->id,
        'code' => 'TEST1',
    ]);

    expect(fn () => Gatepass::factory()->create([
        'organization_id' => $organization->id,
        'code' => 'TEST1',
    ]))->toThrow(QueryException::class);
});

it('allows same code in different organizations', function () {
    $organization1 = Organization::factory()->create();
    $organization2 = Organization::factory()->create();

    $gatepass1 = Gatepass::factory()->create([
        'organization_id' => $organization1->id,
        'code' => 'TEST1',
    ]);

    $gatepass2 = Gatepass::factory()->create([
        'organization_id' => $organization2->id,
        'code' => 'TEST1',
    ]);

    expect($gatepass1->code)->toBe($gatepass2->code)
        ->and($gatepass1->organization_id)->not->toBe($gatepass2->organization_id);
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

it('finds a gatepass by code within organization', function () {
    $service = app(GatepassServiceInterface::class);
    $organization = Organization::factory()->create();
    $activity = Activity::factory()->create(['organization_id' => $organization->id]);
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    $gatepass = Gatepass::factory()->create([
        'organization_id' => $organization->id,
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'FGHIJ',
    ]);

    $found = $service->findByCode('FGHIJ', $organization);

    expect($found)
        ->not->toBeNull()
        ->id->toBe($gatepass->id)
        ->code->toBe('FGHIJ');
});

it('returns null when code does not exist in organization for findByCode', function () {
    $service = app(GatepassServiceInterface::class);
    $organization = Organization::factory()->create();

    $found = $service->findByCode('NOTEX', $organization);

    expect($found)->toBeNull();
});

it('does not find code from different organization', function () {
    $service = app(GatepassServiceInterface::class);
    $organization1 = Organization::factory()->create();
    $organization2 = Organization::factory()->create();

    Gatepass::factory()->create([
        'organization_id' => $organization1->id,
        'code' => 'FGHIJ',
    ]);

    $found = $service->findByCode('FGHIJ', $organization2);

    expect($found)->toBeNull();
});

it('loads relationships when finding by code', function () {
    $service = app(GatepassServiceInterface::class);
    $organization = Organization::factory()->create();
    $activity = Activity::factory()->create([
        'organization_id' => $organization->id,
        'title' => 'Test Activity',
    ]);
    $child = Child::factory()->create(['first_name' => 'TestChild']);
    $guardian = Guardian::factory()->create(['first_name' => 'TestGuardian']);

    Gatepass::factory()->create([
        'organization_id' => $organization->id,
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'code' => 'KLMNO',
    ]);

    $found = $service->findByCode('KLMNO', $organization);

    expect($found)
        ->not->toBeNull()
        ->child->not->toBeNull()
        ->child->first_name->toBe('TestChild')
        ->guardian->not->toBeNull()
        ->guardian->first_name->toBe('TestGuardian')
        ->activity->not->toBeNull()
        ->activity->title->toBe('Test Activity');
});

it('finds a gatepass by ulid', function () {
    $service = app(GatepassServiceInterface::class);
    $gatepass = Gatepass::factory()->create();

    $found = $service->findByUlid($gatepass->id);

    expect($found)
        ->not->toBeNull()
        ->id->toBe($gatepass->id);
});

it('returns null when ulid does not exist', function () {
    $service = app(GatepassServiceInterface::class);

    $found = $service->findByUlid('01HZFAKEULID123456789ABC');

    expect($found)->toBeNull();
});

it('loads relationships when finding by ulid', function () {
    $service = app(GatepassServiceInterface::class);
    $activity = Activity::factory()->create(['title' => 'Test Activity']);
    $child = Child::factory()->create(['first_name' => 'TestChild']);
    $guardian = Guardian::factory()->create(['first_name' => 'TestGuardian']);

    $gatepass = Gatepass::factory()->create([
        'activity_id' => $activity->id,
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
    ]);

    $found = $service->findByUlid($gatepass->id);

    expect($found)
        ->not->toBeNull()
        ->child->not->toBeNull()
        ->child->first_name->toBe('TestChild')
        ->guardian->not->toBeNull()
        ->guardian->first_name->toBe('TestGuardian')
        ->activity->not->toBeNull()
        ->activity->title->toBe('Test Activity');
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
