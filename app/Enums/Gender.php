<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Gender: int implements HasColor, HasIcon, HasLabel
{
    case Male = 1;
    case Female = 0;

    public function getLabel(): string
    {
        return match ($this) {
            self::Male => 'Male',
            self::Female => 'Female',
        };
    }

    /** @return array<int,string> */
    public function getColor(): array
    {
        return match ($this) {
            self::Male => Color::Violet,
            self::Female => Color::Rose
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Male => 'fas-male',
            self::Female => 'fas-female',
        };
    }
}
