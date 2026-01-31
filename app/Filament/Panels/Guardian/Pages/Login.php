<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Pages;

use Filament\Auth\Pages\Login as AuthLogin;

final class Login extends AuthLogin
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => strtolower($data['email']),
            'password' => $data['password'],
        ];
    }
}
