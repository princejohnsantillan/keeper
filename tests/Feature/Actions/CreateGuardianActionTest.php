<?php

declare(strict_types=1);

use App\Actions\CreateGuardianAction;
use App\Enums\Relationship;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\User;

it('creates a guardian with owner_id from authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $child = Child::factory()->create();

    $action = app(CreateGuardianAction::class);

    $guardian = $action([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birth_date' => '1985-06-15',
        'gender' => true,
        'email' => 'john.doe@example.com',
        'phone' => '555-1234',
    ], [
        $child->id => ['relationship' => Relationship::Father->value],
    ]);

    expect($guardian)
        ->toBeInstanceOf(Guardian::class)
        ->first_name->toBe('John')
        ->last_name->toBe('Doe')
        ->owner_id->toBe($user->id);

    $this->assertDatabaseHas(Guardian::class, [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'owner_id' => $user->id,
    ]);
});

it('syncs children relationships', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $child1 = Child::factory()->create();
    $child2 = Child::factory()->create();

    $action = app(CreateGuardianAction::class);

    $guardian = $action([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'birth_date' => '1980-03-20',
        'gender' => false,
        'email' => 'jane.smith@example.com',
        'phone' => '555-5678',
    ], [
        $child1->id => ['relationship' => Relationship::Mother->value],
        $child2->id => ['relationship' => Relationship::Grandmother->value],
    ]);

    expect($guardian->children)->toHaveCount(2);

    $this->assertDatabaseHas('relationships', [
        'guardian_id' => $guardian->id,
        'child_id' => $child1->id,
        'relationship' => Relationship::Mother->value,
    ]);

    $this->assertDatabaseHas('relationships', [
        'guardian_id' => $guardian->id,
        'child_id' => $child2->id,
        'relationship' => Relationship::Grandmother->value,
    ]);
});

it('creates a guardian with empty sync data', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $action = app(CreateGuardianAction::class);

    $guardian = $action([
        'first_name' => 'Solo',
        'last_name' => 'Guardian',
        'birth_date' => '1990-01-01',
        'gender' => true,
        'email' => 'solo.guardian@example.com',
        'phone' => '555-9999',
    ], []);

    expect($guardian)
        ->toBeInstanceOf(Guardian::class)
        ->first_name->toBe('Solo')
        ->last_name->toBe('Guardian');

    expect($guardian->children)->toHaveCount(0);

    $this->assertDatabaseHas(Guardian::class, [
        'id' => $guardian->id,
        'first_name' => 'Solo',
        'last_name' => 'Guardian',
    ]);
});
