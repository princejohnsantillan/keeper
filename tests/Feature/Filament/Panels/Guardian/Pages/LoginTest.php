<?php

declare(strict_types=1);

use App\Filament\Panels\Guardian\Pages\Login;

it('lowercases email in credentials', function () {
    $login = new Login;

    $method = new ReflectionMethod($login, 'getCredentialsFromFormData');

    $credentials = $method->invoke($login, [
        'email' => 'GUARDIAN@EXAMPLE.COM',
        'password' => 'secret',
    ]);

    expect($credentials)->toBe([
        'email' => 'guardian@example.com',
        'password' => 'secret',
    ]);
});

it('preserves lowercase email in credentials', function () {
    $login = new Login;

    $method = new ReflectionMethod($login, 'getCredentialsFromFormData');

    $credentials = $method->invoke($login, [
        'email' => 'guardian@example.com',
        'password' => 'secret',
    ]);

    expect($credentials)->toBe([
        'email' => 'guardian@example.com',
        'password' => 'secret',
    ]);
});

it('handles mixed case email in credentials', function () {
    $login = new Login;

    $method = new ReflectionMethod($login, 'getCredentialsFromFormData');

    $credentials = $method->invoke($login, [
        'email' => 'Guardian@Example.COM',
        'password' => 'secret',
    ]);

    expect($credentials)->toBe([
        'email' => 'guardian@example.com',
        'password' => 'secret',
    ]);
});
