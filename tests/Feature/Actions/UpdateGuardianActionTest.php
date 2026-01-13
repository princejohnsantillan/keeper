<?php

declare(strict_types=1);

use App\Actions\UpdateGuardianAction;
use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship as RelationshipModel;

it('updates a guardian without syncing children', function () {
    $guardian = Guardian::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
    $action = app(UpdateGuardianAction::class);

    $updatedGuardian = $action($guardian, [
        'first_name' => 'Jane',
        'phone' => '1234567890',
    ]);

    expect($updatedGuardian)
        ->first_name->toBe('Jane')
        ->phone->toBe('1234567890')
        ->last_name->toBe('Doe');

    $this->assertDatabaseHas(Guardian::class, [
        'id' => $guardian->id,
        'first_name' => 'Jane',
        'phone' => '1234567890',
    ]);
});

it('updates a guardian and syncs children relationships', function () {
    $guardian = Guardian::factory()->create();
    $child1 = Child::factory()->create();
    $child2 = Child::factory()->create();

    $action = app(UpdateGuardianAction::class);

    $updatedGuardian = $action(
        $guardian,
        ['first_name' => 'Updated Name'],
        [
            $child1->id => ['relationship' => Relationship::Father->value],
            $child2->id => ['relationship' => Relationship::Uncle->value],
        ]
    );

    expect($updatedGuardian->first_name)->toBe('Updated Name');

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
        'relationship' => Relationship::Father->value,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child2->id,
        'relationship' => Relationship::Uncle->value,
    ]);
});

it('replaces existing children relationships when syncing', function () {
    $guardian = Guardian::factory()->create();
    $child1 = Child::factory()->create();
    $child2 = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
        'relationship' => Relationship::Father,
    ]);

    $action = app(UpdateGuardianAction::class);

    $action(
        $guardian,
        ['first_name' => 'New Name'],
        [
            $child2->id => ['relationship' => Relationship::Uncle->value],
        ]
    );

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child2->id,
        'relationship' => Relationship::Uncle->value,
    ]);
});
