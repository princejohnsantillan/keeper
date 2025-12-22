<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\Textarea;

final class AppTextarea
{
    public static function notes(string $field = 'notes', $abel = 'Notes'): Textarea
    {
        return Textarea::make($field)->label($abel);
    }
}
