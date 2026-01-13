<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolists;

use App\Avatar;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

final class AppSpatieMediaLibraryImageEntry
{
    public static function avatar(string $field = 'avatar', ?string $label = null): SpatieMediaLibraryImageEntry
    {
        return SpatieMediaLibraryImageEntry::make($field)->label($label)
            ->hiddenLabel()
            ->collection('avatar')
            ->circular()
            ->size(120)
            ->defaultImageUrl(fn ($record): string => Avatar::generateUrl($record->full_name));
    }

    public static function thumbnail(string $field = 'thumbnail', ?string $label = null): SpatieMediaLibraryImageEntry
    {
        return SpatieMediaLibraryImageEntry::make($field)->label($label)
            ->hiddenLabel()
            ->collection('thumbnail')
            ->conversion('thumbnail');
    }
}
