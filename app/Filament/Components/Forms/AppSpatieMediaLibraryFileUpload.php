<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

final class AppSpatieMediaLibraryFileUpload
{
    public static function avatar(string $field = 'avatar', ?string $label = null): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make($field)->label($label)
            ->collection('avatar')
            ->image()
            ->avatar()
            ->imageEditor()
            ->circleCropper()
            ->required();
    }

    public static function thumbnail(string $field = 'thumbnail', ?string $label = null): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make($field)->label($label)
            ->collection('thumbnail')
            ->image()
            ->imageEditor()
            ->imageEditorAspectRatios([
                '16:9',
            ])
            ->required();
    }
}
