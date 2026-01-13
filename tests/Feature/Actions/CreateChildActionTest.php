<?php

declare(strict_types=1);

use App\Actions\CreateChildAction;
use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship as RelationshipModel;

it('creates a child and establishes a relationship with the guardian', function () {
    $guardian = Guardian::factory()->create();
    $action = app(CreateChildAction::class);

    $child = $action(
        [
            'first_name' => 'Alice',
            'last_name' => 'Doe',
            'birth_date' => '2018-06-15',
            'gender' => false,
        ],
        $guardian,
        Relationship::Mother
    );

    expect($child)
        ->toBeInstanceOf(Child::class)
        ->first_name->toBe('Alice')
        ->last_name->toBe('Doe');

    $this->assertDatabaseHas(Child::class, [
        'first_name' => 'Alice',
        'last_name' => 'Doe',
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Mother->value,
        'is_primary' => true,
    ]);
});

it('creates a child with optional fields', function () {
    $guardian = Guardian::factory()->create();
    $action = app(CreateChildAction::class);

    $child = $action(
        [
            'first_name' => 'Bob',
            'last_name' => 'Smith',
            'middle_name' => 'James',
            'nickname' => 'Bobby',
            'birth_date' => '2015-08-20',
            'gender' => true,
            'notes' => 'Allergic to peanuts',
        ],
        $guardian,
        Relationship::Father
    );

    expect($child)
        ->toBeInstanceOf(Child::class)
        ->first_name->toBe('Bob')
        ->last_name->toBe('Smith')
        ->middle_name->toBe('James')
        ->nickname->toBe('Bobby')
        ->notes->toBe('Allergic to peanuts');

    $this->assertDatabaseHas(Child::class, [
        'id' => $child->id,
        'first_name' => 'Bob',
        'last_name' => 'Smith',
        'middle_name' => 'James',
        'nickname' => 'Bobby',
        'notes' => 'Allergic to peanuts',
    ]);
});

it('sets is_primary to true for the relationship', function () {
    $guardian = Guardian::factory()->create();
    $action = app(CreateChildAction::class);

    $child = $action(
        [
            'first_name' => 'Charlie',
            'last_name' => 'Brown',
            'birth_date' => '2020-01-10',
            'gender' => true,
        ],
        $guardian,
        Relationship::Guardian
    );

    $relationship = RelationshipModel::query()
        ->where('guardian_id', $guardian->id)
        ->where('child_id', $child->id)
        ->first();

    expect($relationship)
        ->not->toBeNull()
        ->is_primary->toBeTruthy();
});
