<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\TextInput;

final class AppTextInput
{
    public static function firstName(string $field = 'first_name', string $label = 'First name'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->required()
            ->rules(['max:80']);
    }

    public static function middleName(string $field = 'middle_name', string $label = 'Middle name'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->rules(['max:80']);
    }

    public static function lastName(string $field = 'last_name', string $label = 'Last name'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->required()
            ->rules(['max:80']);
    }

    public static function nickname(string $field = 'nickname', string $label = 'Nickname'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->rules(['max:40']);
    }

    public static function email(string $field = 'email', string $label = 'Email'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->email()
            ->required();
    }

    public static function phone(string $field = 'phone', string $label = 'Phone'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->tel()
            ->rules(['max:16']);
    }
}
