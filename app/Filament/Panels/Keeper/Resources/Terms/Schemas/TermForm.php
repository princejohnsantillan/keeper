<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Terms\Schemas;

use App\Filament\Components\Forms\AppMarkdownEditor;
use App\Filament\Components\Forms\AppTextInput;
use Filament\Schemas\Schema;

final class TermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppTextInput::name()
                    ->columnSpanFull(),
                AppMarkdownEditor::content()
                    ->columnSpanFull(),
            ]);
    }
}
