<?php

namespace App\Filament\Components;

use Filament\Forms\Components\TextInput as FilamentTextInput;

final class TextInput
{
    public static function firstName(): FilamentTextInput
    {
        return FilamentTextInput::make('first_name')
            ->required()
            ->rules(['string', 'max:80']);
    }

    public static function middleName(): FilamentTextInput
    {
        return FilamentTextInput::make('middle_name')
            ->rules(['string', 'max:80']);
    }

    public static function lastName(): FilamentTextInput
    {
        return FilamentTextInput::make('last_name')
            ->required()
            ->rules(['string', 'max:80']);
    }

    public static function nickname(): FilamentTextInput
    {
        return FilamentTextInput::make('nickname')
            ->rules(['string', 'max:40']);
    }
}
