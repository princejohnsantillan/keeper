<?php

declare(strict_types=1);

use App\Actions\DetachChildFromGuardianAction;
use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship as RelationshipModel;

it('detaches a child from a guardian', function () {
    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    RelationshipModel::factory()->create([
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
        'relationship' => Relationship::Father,
    ]);

    $action = app(DetachChildFromGuardianAction::class);

    $action($child, $guardian);

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'guardian_id' => $guardian->id,
        'child_id' => $child->id,
    ]);

    $this->assertDatabaseHas(Child::class, [
        'id' => $child->id,
    ]);

    $this->assertDatabaseHas(Guardian::class, [
        'id' => $guardian->id,
    ]);
});

it('only removes the specific guardian-child relationship', function () {
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

    $this->assertDatabaseMissing(RelationshipModel::class, [
        'guardian_id' => $guardian1->id,
        'child_id' => $child->id,
    ]);

    $this->assertDatabaseHas(RelationshipModel::class, [
        'guardian_id' => $guardian2->id,
        'child_id' => $child->id,
    ]);
});

it('does nothing if relationship does not exist', function () {
    $guardian = Guardian::factory()->create();
    $child = Child::factory()->create();

    $action = app(DetachChildFromGuardianAction::class);

    $action($child, $guardian);

    expect(RelationshipModel::query()->count())->toBe(0);
});
