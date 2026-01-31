<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum KeeperRole: string implements HasColor, HasIcon, HasLabel
{
    case Admin = 'admin';
    case Gatekeeper = 'gatekeeper';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Gatekeeper => 'Gatekeeper',
        };
    }

    /** @return array<int,string> */
    public function getColor(): array
    {
        return match ($this) {
            self::Admin => Color::Amber,
            self::Gatekeeper => Color::Blue,
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Admin => 'heroicon-o-shield-check',
            self::Gatekeeper => 'heroicon-o-user',
        };
    }
}
