<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Messages\Schemas;

use App\Filament\Components\Forms\AppRichEditor;
use App\Filament\Components\Forms\AppTextInput;
use Filament\Schemas\Schema;

final class MessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppTextInput::name()
                    ->columnSpanFull(),
                AppRichEditor::content()
                    ->columnSpanFull(),
            ]);
    }
}
