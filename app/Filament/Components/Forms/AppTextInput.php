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

    public static function title(string $field = 'title', string $label = 'Title'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->required();
    }

    public static function name(string $field = 'name', string $label = 'Name'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->required()
            ->maxLength(255);
    }

    public static function location(string $field = 'location', string $label = 'Location'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->required();
    }

    public static function type(string $field = 'type', string $label = 'Type'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->maxLength(255)
            ->helperText('Optional. Use to categorize tags (e.g., "child", "guardian").');
    }
}
