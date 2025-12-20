<?php

namespace App\Filament\Keeper\Resources\Gatepasses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GatepassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('guardian_id')
                    ->relationship('guardian', 'id')
                    ->required(),
                Select::make('child_id')
                    ->relationship('child', 'id')
                    ->required(),
                Select::make('activity_id')
                    ->relationship('activity', 'title')
                    ->required(),
                TextInput::make('code')
                    ->required(),
            ]);
    }
}
