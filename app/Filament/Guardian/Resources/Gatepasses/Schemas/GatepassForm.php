<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Gatepasses\Schemas;

use App\ReadableCode;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class GatepassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('guardian_id')
                    ->relationship('guardian', 'first_name')

                    ->required(),
                Select::make('child_id')
                    ->relationship('child', 'first_name')
                    ->required(),
                Select::make('activity_id')
                    ->relationship('activity', 'title')
                    ->required(),
                TextInput::make('code')
                    ->default(ReadableCode::generate())
                    ->required(),
            ]);
    }
}
