<?php

declare(strict_types=1);

use App\Actions\CreateChildAction;
use App\Models\Child;
use App\Models\User;

it('creates a child with owner_id from authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $action = app(CreateChildAction::class);

    $child = $action([
        'first_name' => 'Alice',
        'last_name' => 'Doe',
        'birth_date' => '2018-06-15',
        'gender' => false,
    ]);

    expect($child)
        ->toBeInstanceOf(Child::class)
        ->first_name->toBe('Alice')
        ->last_name->toBe('Doe')
        ->owner_id->toBe($user->id);

    $this->assertDatabaseHas(Child::class, [
        'first_name' => 'Alice',
        'last_name' => 'Doe',
        'owner_id' => $user->id,
    ]);
});

it('creates a child with optional fields', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $action = app(CreateChildAction::class);

    $child = $action([
        'first_name' => 'Bob',
        'last_name' => 'Smith',
        'middle_name' => 'James',
        'nickname' => 'Bobby',
        'birth_date' => '2015-08-20',
        'gender' => true,
        'notes' => 'Allergic to peanuts',
    ]);

    expect($child)
        ->toBeInstanceOf(Child::class)
        ->first_name->toBe('Bob')
        ->last_name->toBe('Smith')
        ->middle_name->toBe('James')
        ->nickname->toBe('Bobby')
        ->notes->toBe('Allergic to peanuts')
        ->owner_id->toBe($user->id);

    $this->assertDatabaseHas(Child::class, [
        'id' => $child->id,
        'first_name' => 'Bob',
        'last_name' => 'Smith',
        'middle_name' => 'James',
        'nickname' => 'Bobby',
        'notes' => 'Allergic to peanuts',
        'owner_id' => $user->id,
    ]);
});
