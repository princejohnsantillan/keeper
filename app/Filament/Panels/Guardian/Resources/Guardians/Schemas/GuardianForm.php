<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Schemas;

use App\Enums\Gender;
use App\Enums\Relationship;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
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
                        TextInput::make('first_name')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('middle_name')
                            ->columnSpan(2),
                        TextInput::make('last_name')
                            ->required()
                            ->columnSpan(2),
                        DatePicker::make('birth_date')
                            ->displayFormat('d M Y')
                            ->native(false)
                            ->columnSpan(3),
                        ToggleButtons::make('gender')
                            ->required()
                            ->options(Gender::class)
                            ->inline()
                            ->columnSpan(3),

                        TextInput::make('email')
                            ->email()
                            ->required()->columnSpan(3),
                        TextInput::make('phone')
                            ->tel()->columnSpan(3),
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
