<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Schemas;

use App\Enums\Relationship;
use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppSpatieMediaLibraryFileUpload;
use App\Filament\Components\Forms\AppTagsInput;
use App\Filament\Components\Forms\AppTextarea;
use App\Filament\Components\Forms\AppTextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

final class ChildForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppSpatieMediaLibraryFileUpload::avatar()
                    ->columnSpanFull(),
                Fieldset::make('Child Details')
                    ->schema([
                        AppTextInput::firstName(),
                        AppTextInput::middleName(),
                        AppTextInput::lastName(),
                        AppTextInput::nickname(),
                        AppDatePicker::birthDate(),
                        AppSelect::gender(),
                        AppTagsInput::tags()
                            ->columnSpanFull(),
                        AppTextarea::notes('notes', "Guardian's notes")
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Fieldset::make('Relationships with Guardians')
                    ->schema([
                        self::guardiansRepeater()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function guardiansRepeater(): Repeater
    {
        return Repeater::make('guardians')
            ->hiddenLabel()
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->minItems(1)
            ->schema([
                self::guardianIdHidden(),
                self::guardianNameInput(),
                self::relationshipSelect(),
            ])
            ->columns(2);
    }

    private static function guardianIdHidden(): Hidden
    {
        return Hidden::make('guardian_id')
            ->required();
    }

    private static function guardianNameInput(): TextInput
    {
        return TextInput::make('guardian_name')
            ->label('Guardian')
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
