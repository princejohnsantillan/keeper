<?php

declare(strict_types=1);

use App\Actions\DetachGuardianFromChildrenAction;
use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship as RelationshipModel;

it('detaches a guardian from specified children', function () {
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

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child2->id,
    ]);
});

it('detaches a guardian from multiple children', function () {
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

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
    ]);

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child2->id,
    ]);
});

it('does not affect other guardian relationships', function () {
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

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'guardian_id' => $guardian1->id,
        'child_id' => $child->id,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian2->id,
        'child_id' => $child->id,
    ]);
});

it('accepts a collection of child ids', function () {
    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Guardian,
    ]);

    $action = app(DetachGuardianFromChildrenAction::class);

    $action($guardian, collect([$child->id]));

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
    ]);
});
