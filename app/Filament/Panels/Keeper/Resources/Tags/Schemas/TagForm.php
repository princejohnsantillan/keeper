<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Tags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('type')
                    ->maxLength(255)
                    ->helperText('Optional. Use to categorize tags (e.g., "child", "guardian").'),
            ]);
    }
}
