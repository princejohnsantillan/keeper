<?php

declare(strict_types=1);

use App\Actions\WalkInRegistrationAction;
use App\Enums\Relationship;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Models\Relationship as RelationshipModel;

it('registers a walk-in guardian and child for an activity', function () {
    $activity = Activity::factory()->create();
    $action = app(WalkInRegistrationAction::class);

    $guardianData = [
        'first_name' => 'John',
        'middle_name' => 'William',
        'last_name' => 'Doe',
        'birth_date' => '1985-03-15',
        'gender' => true,
        'email' => 'john.doe@example.com',
        'phone' => '1234567890',
    ];

    $childData = [
        'first_name' => 'Alice',
        'middle_name' => 'Marie',
        'last_name' => 'Doe',
        'nickname' => 'Ali',
        'birth_date' => '2018-06-20',
        'gender' => false,
        'notes' => 'Allergic to peanuts',
    ];

    $gatepass = $action($guardianData, $childData, Relationship::Father, $activity);

    expect($gatepass)
        ->toBeInstanceOf(Gatepass::class)
        ->activity_id->toBe($activity->id)
        ->code->toBeString()
        ->code->toHaveLength(5);

    $this->assertDatabaseHas(Guardian::class, [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
    ]);

    $this->assertDatabaseHas(Child::class, [
        'first_name' => 'Alice',
        'last_name' => 'Doe',
        'nickname' => 'Ali',
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $gatepass->guardian_id,
        'child_id' => $gatepass->child_id,
        'relationship' => Relationship::Father->value,
    ]);

    $this->assertDatabaseHas(Gatepass::class, [
        'guardian_id' => $gatepass->guardian_id,
        'child_id' => $gatepass->child_id,
        'activity_id' => $activity->id,
    ]);
});

it('creates all records in a transaction', function () {
    $activity = Activity::factory()->create();
    $action = app(WalkInRegistrationAction::class);

    $guardianData = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'birth_date' => '1980-05-20',
        'gender' => false,
        'email' => 'jane.smith@example.com',
        'phone' => '9876543210',
    ];

    $childData = [
        'first_name' => 'Bobby',
        'last_name' => 'Smith',
        'birth_date' => '2015-01-10',
        'gender' => true,
    ];

    $gatepass = $action($guardianData, $childData, Relationship::Mother, $activity);

    $guardian = Guardian::query()->find($gatepass->guardian_id);
    $child = Child::query()->find($gatepass->child_id);

    expect($guardian)->not->toBeNull();
    expect($child)->not->toBeNull();
    expect($guardian->children)->toHaveCount(1);
    expect($guardian->children->first()->id)->toBe($child->id);
});

it('creates gatepass with unique code', function () {
    $activity = Activity::factory()->create();
    $action = app(WalkInRegistrationAction::class);

    $gatepass1 = $action(
        ['first_name' => 'Parent1', 'last_name' => 'Test', 'birth_date' => '1975-01-01', 'gender' => true, 'email' => 'parent1@test.com', 'phone' => '1111111111'],
        ['first_name' => 'Child1', 'last_name' => 'Test', 'birth_date' => '2020-01-01', 'gender' => true],
        Relationship::Guardian,
        $activity
    );

    $gatepass2 = $action(
        ['first_name' => 'Parent2', 'last_name' => 'Test', 'birth_date' => '1975-01-01', 'gender' => true, 'email' => 'parent2@test.com', 'phone' => '2222222222'],
        ['first_name' => 'Child2', 'last_name' => 'Test', 'birth_date' => '2020-01-01', 'gender' => false],
        Relationship::Guardian,
        $activity
    );

    expect($gatepass1->code)->not->toBe($gatepass2->code);
});
