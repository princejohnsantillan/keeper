<?php

declare(strict_types=1);

use App\Actions\UpdateChildAction;
use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship as RelationshipModel;

it('updates a child without relationship', function () {
    $child = Child::factory()->create([
        'first_name' => 'Alice',
        'last_name' => 'Doe',
    ]);
    $action = app(UpdateChildAction::class);

    $updatedChild = $action($child, [
        'first_name' => 'Bob',
        'nickname' => 'Bobby',
    ]);

    expect($updatedChild)
        ->first_name->toBe('Bob')
        ->nickname->toBe('Bobby')
        ->last_name->toBe('Doe');

    $this->assertDatabaseHas(Child::class, [
        'id' => $child->id,
        'first_name' => 'Bob',
        'nickname' => 'Bobby',
    ]);
});

it('updates a child with relationship', function () {
    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Father,
    ]);

    $action = app(UpdateChildAction::class);

    $updatedChild = $action(
        $child,
        ['first_name' => 'Updated Name'],
        $guardian,
        Relationship::Uncle
    );

    expect($updatedChild->first_name)->toBe('Updated Name');

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Uncle->value,
    ]);
});

it('does not update relationship if guardian is null', function () {
    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Mother,
    ]);

    $action = app(UpdateChildAction::class);

    $action($child, ['first_name' => 'New Name'], null, Relationship::Aunt);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Mother->value,
    ]);
});

it('does not update relationship if relationship is null', function () {
    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Mother,
    ]);

    $action = app(UpdateChildAction::class);

    $action($child, ['first_name' => 'New Name'], $guardian, null);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Mother->value,
    ]);
});
