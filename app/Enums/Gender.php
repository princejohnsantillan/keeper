<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum Gender: int implements HasColor, HasIcon, HasLabel
{
    case Male = 1;
    case Female = 0;

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Male => 'M',
            self::Female => 'F',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Male => Color::Sky,
            self::Female => Color::Rose
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Male => 'fas-male',
            self::Female => 'fas-female',
        };
    }
}
