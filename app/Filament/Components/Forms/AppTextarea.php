<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\Textarea;

final class AppTextarea
{
    public static function notes(string $field = 'notes', string $label = 'Notes'): Textarea
    {
        return Textarea::make($field)->label($label);
    }
}
