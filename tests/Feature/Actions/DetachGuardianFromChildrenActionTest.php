<?php

declare(strict_types=1);

use App\Actions\DetachGuardianFromChildrenAction;
use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship as RelationshipModel;

it('soft deletes a guardian while preserving relationships', function () {
    $guardian = Guardian::factory()->create();
    $child1 = Child::factory()->create();
    $child2 = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
        'relationship' => Relationship::Father,
    ]);

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child2->id,
        'relationship' => Relationship::Father,
    ]);

    $action = app(DetachGuardianFromChildrenAction::class);

    $action($guardian, [$child1->id]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child2->id,
    ]);

    $this->assertSoftDeleted(Guardian::class, [
        'id' => $guardian->id,
    ]);
});

it('preserves all relationships when soft deleting a guardian', function () {
    $guardian = Guardian::factory()->create();
    $child1 = Child::factory()->create();
    $child2 = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
        'relationship' => Relationship::Mother,
    ]);

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child2->id,
        'relationship' => Relationship::Mother,
    ]);

    $action = app(DetachGuardianFromChildrenAction::class);

    $action($guardian, [$child1->id, $child2->id]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child2->id,
    ]);

    $this->assertSoftDeleted(Guardian::class, [
        'id' => $guardian->id,
    ]);
});

it('preserves all relationships and only soft deletes the target guardian', function () {
    $guardian1 = Guardian::factory()->create();
    $guardian2 = Guardian::factory()->create();
    $child = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian1->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Father,
    ]);

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian2->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Mother,
    ]);

    $action = app(DetachGuardianFromChildrenAction::class);

    $action($guardian1, [$child->id]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian1->id,
        'child_id' => $child->id,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian2->id,
        'child_id' => $child->id,
    ]);

    $this->assertSoftDeleted(Guardian::class, [
        'id' => $guardian1->id,
    ]);

    $this->assertNotSoftDeleted(Guardian::class, [
        'id' => $guardian2->id,
    ]);
});

it('accepts a collection of child ids and preserves relationships', function () {
    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Guardian,
    ]);

    $action = app(DetachGuardianFromChildrenAction::class);

    $action($guardian, collect([$child->id]));

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
    ]);

    $this->assertSoftDeleted(Guardian::class, [
        'id' => $guardian->id,
    ]);
});
