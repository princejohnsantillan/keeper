<?php

declare(strict_types=1);

use App\Actions\SyncChildGuardiansAction;
use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship as RelationshipModel;

it('syncs guardians for a child', function () {
    $child = Child::factory()->create();
    $guardian1 = Guardian::factory()->create();
    $guardian2 = Guardian::factory()->create();

    $action = app(SyncChildGuardiansAction::class);

    $action($child, [
        $guardian1->id => ['relationship' => Relationship::Father->value],
        $guardian2->id => ['relationship' => Relationship::Mother->value],
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'child_id' => $child->id,
        'guardian_id' => $guardian1->id,
        'relationship' => Relationship::Father->value,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'child_id' => $child->id,
        'guardian_id' => $guardian2->id,
        'relationship' => Relationship::Mother->value,
    ]);
});

it('removes guardians not in sync data', function () {
    $child = Child::factory()->create();
    $guardian1 = Guardian::factory()->create();
    $guardian2 = Guardian::factory()->create();

    RelationshipModel::factory()->create([
        'child_id' => $child->id,
        'guardian_id' => $guardian1->id,
        'relationship' => Relationship::Father,
    ]);

    RelationshipModel::factory()->create([
        'child_id' => $child->id,
        'guardian_id' => $guardian2->id,
        'relationship' => Relationship::Mother,
    ]);

    $action = app(SyncChildGuardiansAction::class);

    $action($child, [
        $guardian1->id => ['relationship' => Relationship::Uncle->value],
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'child_id' => $child->id,
        'guardian_id' => $guardian1->id,
        'relationship' => Relationship::Uncle->value,
    ]);

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'child_id' => $child->id,
        'guardian_id' => $guardian2->id,
    ]);
});

it('can remove all guardians by syncing empty array', function () {
    $child = Child::factory()->create();
    $guardian = Guardian::factory()->create();

    RelationshipModel::factory()->create([
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
        'relationship' => Relationship::Father,
    ]);

    $action = app(SyncChildGuardiansAction::class);

    $action($child, []);

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'child_id' => $child->id,
        'guardian_id' => $guardian->id,
    ]);
});
