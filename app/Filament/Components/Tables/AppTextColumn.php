<?php

declare(strict_types=1);

namespace App\Filament\Components\Tables;

use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;

final class AppTextColumn
{
    public static function firstName(string $field = 'first_name', string $label = 'First name'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable()
            ->sortable();
    }

    public static function middleName(string $field = 'middle_name', string $label = 'Middle name'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable()
            ->sortable();
    }

    public static function lastName(string $field = 'last_name', string $label = 'Last name'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable()
            ->sortable();
    }

    public static function fullName(string $field = 'full_name', string $label = 'Full name'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable()
            ->sortable();
    }

    public static function nickname(string $field = 'nickname', string $label = 'Nickname'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable()
            ->sortable();
    }

    public static function email(string $field = 'email', string $label = 'Email'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable()
            ->sortable();
    }

    public static function phone(string $field = 'phone', string $label = 'Phone'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable();
    }

    public static function birthDate(string $field = 'birth_date', string $label = 'Birth date'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->date('d M Y')
            ->description(fn ($record): string => "{$record->birth_date->age} yrs")
            ->sortable();
    }

    public static function title(string $field = 'title', string $label = 'Title'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable()
            ->sortable();
    }

    public static function location(string $field = 'location', string $label = 'Location'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable();
    }

    public static function code(string $field = 'code', string $label = 'Code'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->badge()
            ->copyable()
            ->size(TextSize::Large)
            ->searchable()
            ->sortable();
    }

    public static function name(string $field = 'name', string $label = 'Name'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable()
            ->sortable();
    }

    public static function type(string $field = 'type', string $label = 'Type'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->badge()
            ->searchable()
            ->sortable();
    }

    public static function createdAt(string $field = 'created_at', string $label = 'Created at'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }

    public static function updatedAt(string $field = 'updated_at', string $label = 'Updated at'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
    }
}
