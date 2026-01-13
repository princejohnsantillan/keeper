<?php

declare(strict_types=1);

namespace App\Filament\Components\Tables;

use App\Avatar;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

final class AppSpatieMediaLibraryImageColumn
{
    public static function avatar(string $field = 'avatar', ?string $label = null): SpatieMediaLibraryImageColumn
    {
        return SpatieMediaLibraryImageColumn::make($field)->label($label)
            ->collection('avatar')
            ->circular()
            ->defaultImageUrl(fn ($record): string => Avatar::generateUrl($record->full_name));
    }

    public static function thumbnail(string $field = 'thumbnail', ?string $label = null): SpatieMediaLibraryImageColumn
    {
        return SpatieMediaLibraryImageColumn::make($field)->label($label)
            ->collection('thumbnail')
            ->conversion('thumbnail');
    }
}
