<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum KeeperStatus: string implements HasLabel
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Pending => 'Pending',
        };
    }
}
