<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Guardians\Schemas;

use App\Enums\Relationship;
use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppSpatieMediaLibraryFileUpload;
use App\Filament\Components\Forms\AppTagsInput;
use App\Filament\Components\Forms\AppTextInput;
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
                AppSpatieMediaLibraryFileUpload::avatar()
                    ->columnSpanFull(),
                Fieldset::make('Guardian Details')
                    ->schema([
                        AppTextInput::firstName()
                            ->columnSpan(2),
                        AppTextInput::middleName()
                            ->columnSpan(2),
                        AppTextInput::lastName()
                            ->columnSpan(2),
                        AppDatePicker::birthDate()
                            ->maxDate(now()->subYears(18))
                            ->columnSpan(3),
                        AppSelect::gender()
                            ->columnSpan(3),
                        AppTextInput::email()
                            ->columnSpan(3),
                        AppTextInput::phone()
                            ->columnSpan(3),
                        AppTagsInput::tags()
                            ->columnSpan(6),
                    ])
                    ->columns(6)
                    ->columnSpanFull(),
                Fieldset::make('Relationships with Children')
                    ->schema([
                        self::childrenRepeater()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function childrenRepeater(): Repeater
    {
        return Repeater::make('children')
            ->hiddenLabel()
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->minItems(1)
            ->schema([
                self::childIdHidden(),
                self::childNameInput(),
                self::relationshipSelect(),
            ])
            ->columns(2);
    }

    private static function childIdHidden(): Hidden
    {
        return Hidden::make('child_id')
            ->required();
    }

    private static function childNameInput(): TextInput
    {
        return TextInput::make('child_name')
            ->label('Child')
            ->disabled();
    }

    private static function relationshipSelect(): Select
    {
        return Select::make('relationship')
            ->options(Relationship::class)
            ->placeholder('Select a relationship')
            ->native(false);
    }
}
