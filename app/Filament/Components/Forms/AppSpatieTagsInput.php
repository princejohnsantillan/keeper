<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\SpatieTagsInput;

final class AppSpatieTagsInput
{
    public static function tags(string $field = 'tags', ?string $label = null): SpatieTagsInput
    {
        return SpatieTagsInput::make($field)->label($label);
    }
}
