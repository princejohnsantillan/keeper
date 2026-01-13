<?php

declare(strict_types=1);

namespace App\Filament\Components\Tables;

use Filament\Tables\Columns\IconColumn;

final class AppIconColumn
{
    public static function gender(string $field = 'gender', string $label = 'Gender'): IconColumn
    {
        return IconColumn::make($field)->label($label)
            ->boolean();
    }
}
