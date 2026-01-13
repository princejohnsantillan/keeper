<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolists;

use Filament\Infolists\Components\IconEntry;

final class AppIconEntry
{
    public static function gender(string $field = 'gender', string $label = 'Gender'): IconEntry
    {
        return IconEntry::make($field)->label($label);
    }
}
