<?php

declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\MarkdownEditor;

final class AppMarkdownEditor
{
    public static function content(string $field = 'content', string $label = 'Content'): MarkdownEditor
    {
        return MarkdownEditor::make($field)->label($label)
            ->required();
    }
}
