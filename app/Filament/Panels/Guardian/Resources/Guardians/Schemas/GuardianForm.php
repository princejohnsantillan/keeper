<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Schemas;

use App\Enums\Relationship;
use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppTextInput;
use App\Filament\Components\Forms\AppToggleButtons;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

final class GuardianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('Guardian Details')
                    ->schema([
                        AppTextInput::firstName()->columnSpan(2),
                        AppTextInput::middleName()->columnSpan(2),
                        AppTextInput::lastName()->columnSpan(2),
                        AppDatePicker::birthDate()->columnSpan(3),
                        AppToggleButtons::gender()->columnSpan(3),
                        AppTextInput::email()->columnSpan(3),
                        AppTextInput::phone()->columnSpan(3),
                    ])
                    ->columns(6)->columnSpanFull(),
                Fieldset::make('Relationships with Children')
                    ->schema([
                        Repeater::make('children')
                            ->hiddenLabel()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                Hidden::make('child_id')
                                    ->required(),
                                TextInput::make('child_name')
                                    ->label('Child')
                                    ->disabled()
                                    ->columnSpan(1),
                                Select::make('relationship')
                                    ->options(Relationship::class)
                                    ->placeholder('Select a relationship')
                                    ->native(false)
                                    ->columnSpan(1),
                            ])->columns(2)->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
