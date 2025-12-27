<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\DatePicker;

final class AppDatePicker
{
    public static function birthDate(string $field = 'birth_date', string $label = 'Birth date'): DatePicker
    {
        return DatePicker::make($field)->label($label)
            ->maxDate(now())
            ->displayFormat('d M Y')
            ->required();
    }
}
