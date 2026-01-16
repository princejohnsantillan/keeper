<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\RichEditor;

final class AppRichEditor
{
    public static function content(string $field = 'content', string $label = 'Content'): RichEditor
    {
        return RichEditor::make($field)->label($label)
            ->required();
    }
}
