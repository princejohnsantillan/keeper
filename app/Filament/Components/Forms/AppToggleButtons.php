<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use App\Enums\Gender;
use Filament\Forms\Components\ToggleButtons;

final class AppToggleButtons
{
    public static function gender(string $field = 'gender', ?string $label = 'Gender'): ToggleButtons
    {
        return ToggleButtons::make($field)->label($label)
            ->options(Gender::class)
            ->required()
            ->inline();
    }
}
