<?php

declare(strict_types=1);

use App\Filament\Panels\Keeper\Pages\Login;

it('lowercases email in credentials', function () {
    $login = new Login;

    $method = new ReflectionMethod($login, 'getCredentialsFromFormData');

    $credentials = $method->invoke($login, [
        'email' => 'KEEPER@EXAMPLE.COM',
        'password' => 'secret',
    ]);

    expect($credentials)->toBe([
        'email' => 'keeper@example.com',
        'password' => 'secret',
    ]);
});

it('preserves lowercase email in credentials', function () {
    $login = new Login;

    $method = new ReflectionMethod($login, 'getCredentialsFromFormData');

    $credentials = $method->invoke($login, [
        'email' => 'keeper@example.com',
        'password' => 'secret',
    ]);

    expect($credentials)->toBe([
        'email' => 'keeper@example.com',
        'password' => 'secret',
    ]);
});

it('handles mixed case email in credentials', function () {
    $login = new Login;

    $method = new ReflectionMethod($login, 'getCredentialsFromFormData');

    $credentials = $method->invoke($login, [
        'email' => 'Keeper@Example.COM',
        'password' => 'secret',
    ]);

    expect($credentials)->toBe([
        'email' => 'keeper@example.com',
        'password' => 'secret',
    ]);
});
