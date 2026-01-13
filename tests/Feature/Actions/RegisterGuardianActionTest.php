<?php

declare(strict_types=1);

use App\Actions\RegisterGuardianAction;
use App\Models\Guardian;
use App\Models\User;

it('creates a user and guardian', function () {
    $action = app(RegisterGuardianAction::class);

    $user = $action([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'birth_date' => '1985-03-15',
        'gender' => true,
        'email' => 'john.doe@example.com',
        'password' => 'password123',
    ]);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->name->toBe('John Doe')
        ->email->toBe('john.doe@example.com');

    $this->assertDatabaseHas(User::class, [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ]);

    $this->assertDatabaseHas(Guardian::class, [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.doe@example.com',
        'user_id' => $user->id,
    ]);
});

it('creates a guardian with optional fields', function () {
    $action = app(RegisterGuardianAction::class);

    $user = $action([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'middle_name' => 'Marie',
        'birth_date' => '1990-05-15',
        'gender' => true,
        'phone' => '+1234567890',
        'email' => 'jane.smith@example.com',
        'password' => 'password123',
    ]);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->name->toBe('Jane Smith');

    $guardian = Guardian::query()->where('user_id', $user->id)->first();

    expect($guardian)
        ->first_name->toBe('Jane')
        ->last_name->toBe('Smith')
        ->middle_name->toBe('Marie')
        ->phone->toBe('+1234567890')
        ->email->toBe('jane.smith@example.com');
});

it('trims whitespace from the user name', function () {
    $action = app(RegisterGuardianAction::class);

    $user = $action([
        'first_name' => '  John  ',
        'last_name' => '  Doe  ',
        'birth_date' => '1985-03-15',
        'gender' => true,
        'email' => 'john.trimmed@example.com',
        'password' => 'password123',
    ]);

    expect($user->name)->toBe('John     Doe');
});
