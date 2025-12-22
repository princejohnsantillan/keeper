<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use App\Enums\Relationship;
use Filament\Forms\Components\Select;

final class AppSelect
{
    public static function relationship(string $field = 'relationship', string $label = 'Relationship'): Select
    {
        return Select::make($field)->label($label)
            ->options(Relationship::class)
            ->required();
    }
}
