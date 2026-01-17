<?php

declare(strict_types=1);

use App\Actions\DetachChildFromGuardianAction;
use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship as RelationshipModel;

it('soft deletes a child while preserving relationships', function () {
    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Father,
    ]);

    $action = app(DetachChildFromGuardianAction::class);

    $action($child, $guardian);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
    ]);

    $this->assertSoftDeleted(Child::class, [
        'id' => $child->id,
    ]);

    $this->assertDatabaseHas(Guardian::class, [
        'id' => $guardian->id,
    ]);
});

it('preserves all relationships when soft deleting a child', function () {
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

    $action = app(DetachChildFromGuardianAction::class);

    $action($child, $guardian1);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian1->id,
        'child_id' => $child->id,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian2->id,
        'child_id' => $child->id,
    ]);

    $this->assertSoftDeleted(Child::class, [
        'id' => $child->id,
    ]);
});

it('soft deletes child even if relationship does not exist', function () {
    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    $action = app(DetachChildFromGuardianAction::class);

    $action($child, $guardian);

    expect(RelationshipModel::query()->count())->toBe(0);

    $this->assertSoftDeleted(Child::class, [
        'id' => $child->id,
    ]);
});
