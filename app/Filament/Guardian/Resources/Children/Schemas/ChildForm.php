<?php

namespace App\Filament\Guardian\Resources\Children\Schemas;

use App\Enums\Gender;
use App\Enums\Relationship as RelationshipEnum;
use App\Filament\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class ChildForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::firstName(),
                TextInput::middleName(),
                TextInput::lastName(),
                TextInput::nickname(),
                DatePicker::make('birth_date')
                    ->native(false)
                    ->required(),
                ToggleButtons::make('gender')
                    ->options(Gender::class)
                    ->inline()
                    ->required(),
                Select::make('relationship')
                    ->options(RelationshipEnum::class)
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
