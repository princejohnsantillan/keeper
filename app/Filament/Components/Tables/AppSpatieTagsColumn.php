<?php

declare(strict_types=1);

namespace App\Filament\Components\Tables;

use Filament\Tables\Columns\SpatieTagsColumn;

final class AppSpatieTagsColumn
{
    public static function tags(string $field = 'tags', ?string $label = null): SpatieTagsColumn
    {
        return SpatieTagsColumn::make($field)->label($label);
    }
}
