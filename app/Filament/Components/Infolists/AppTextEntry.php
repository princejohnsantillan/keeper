<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolists;

use Carbon\CarbonImmutable;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

final class AppTextEntry
{
    public static function firstName(string $field = 'first_name', string $label = 'First name'): TextEntry
    {
        return TextEntry::make($field)->label($label);
    }

    public static function middleName(string $field = 'middle_name', string $label = 'Middle name'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->placeholder('—');
    }

    public static function lastName(string $field = 'last_name', string $label = 'Last name'): TextEntry
    {
        return TextEntry::make($field)->label($label);
    }

    public static function fullName(string $field = 'full_name', ?string $label = null): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->hiddenLabel()
            ->size(TextSize::Large)
            ->weight(FontWeight::Bold);
    }

    public static function nickname(string $field = 'nickname', string $label = 'Known as'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->placeholder('—');
    }

    public static function email(string $field = 'email', string $label = 'Email'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->icon('heroicon-o-envelope')
            ->copyable()
            ->placeholder('—');
    }

    public static function phone(string $field = 'phone', string $label = 'Phone'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->icon('heroicon-o-phone')
            ->copyable()
            ->placeholder('—');
    }

    public static function age(string $field = 'birth_date', string $label = 'Age'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->icon('heroicon-o-cake')
            ->formatStateUsing(function (CarbonImmutable $state): string {
                $age = $state->age;
                $years = $age === 1 ? 'year' : 'years';

                return "{$age} {$years} old";
            });
    }

    public static function birthday(string $field = 'birth_date', string $label = 'Birthday'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->icon('heroicon-o-calendar')
            ->date('F j, Y');
    }

    public static function birthDate(string $field = 'birth_date', string $label = 'Birth date'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->date();
    }

    public static function notes(string $field = 'notes', string $label = 'Notes'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->placeholder('No notes recorded.')
            ->prose()
            ->markdown();
    }

    public static function title(string $field = 'title', ?string $label = null): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->hiddenLabel()
            ->size(TextSize::Large)
            ->weight(FontWeight::Bold);
    }

    public static function content(string $field = 'content', ?string $label = null): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->hiddenLabel()
            ->markdown();
    }

    public static function createdAt(string $field = 'created_at', string $label = 'Created at'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->dateTime()
            ->placeholder('—');
    }

    public static function updatedAt(string $field = 'updated_at', string $label = 'Updated at'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->dateTime()
            ->placeholder('—');
    }
}
