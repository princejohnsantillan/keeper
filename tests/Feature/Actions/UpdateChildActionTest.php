<?php

declare(strict_types=1);

use App\Actions\UpdateChildAction;
use App\Models\Child;

it('updates a child', function () {
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

it('preserves fields not included in update', function () {
    $child = Child::factory()->create([
        'first_name' => 'Alice',
        'last_name' => 'Doe',
        'notes' => 'Some notes',
    ]);
    $action = app(UpdateChildAction::class);

    $updatedChild = $action($child, [
        'first_name' => 'Bob',
    ]);

    expect($updatedChild)
        ->first_name->toBe('Bob')
        ->last_name->toBe('Doe')
        ->notes->toBe('Some notes');

    $this->assertDatabaseHas(Child::class, [
        'id' => $child->id,
        'first_name' => 'Bob',
        'last_name' => 'Doe',
        'notes' => 'Some notes',
    ]);
});
