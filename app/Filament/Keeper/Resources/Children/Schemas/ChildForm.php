<?php

namespace App\Filament\Keeper\Resources\Children\Schemas;

use App\Enums\Gender;
use App\Enums\Relationship;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class ChildForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('middle_name'),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('nickname'),
                DatePicker::make('birth_date')
                    ->required(),
                ToggleButtons::make('gender')
                    ->options(Gender::class)
                    ->inline()
                    ->required(),
                Select::make('relationship')
                    ->options(Relationship::class)
                    ->required()
                    ->dehydrated(false)
                    ->visibleOn('create'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
