<?php

declare(strict_types=1);

use App\Models\User;

it('stores email in lowercase', function () {
    $user = User::factory()->create([
        'email' => 'John.Doe@Example.COM',
    ]);

    expect($user->email)->toBe('john.doe@example.com');
});

it('updates email to lowercase', function () {
    $user = User::factory()->create([
        'email' => 'initial@example.com',
    ]);

    $user->update(['email' => 'UPDATED@EXAMPLE.COM']);

    expect($user->fresh()->email)->toBe('updated@example.com');
});
