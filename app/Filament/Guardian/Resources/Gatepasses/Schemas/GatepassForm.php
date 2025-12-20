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
                TextInput::make('code')
                    ->default(ReadableCode::generate())
                    ->disabled()
                    ->copyable()
                    ->required(),

                Select::make('activity_id')
                    ->relationship('activity', 'title')
                    ->required(),

                Select::make('guardian_id')
                    ->relationship('guardian', 'first_name')
                    ->required(),

                Select::make('child_id')
                    ->relationship('child', 'first_name')
                    ->required(),
            ])->columns(2);
    }
}
