<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Schemas;

use App\Enums\Gender;
use App\Enums\Relationship;
use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppTextInput;
use App\Filament\Components\Forms\AppToggleButtons;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

final class ChildForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppTextInput::firstName(),
                AppTextInput::middleName(),
                AppTextInput::lastName(),
                AppTextInput::nickname(),
                AppDatePicker::birthDate(),
                AppToggleButtons::gender(),
                ChildForm::getRelationshipField(),
                ChildForm::getNotesField()->columnSpanFull(),
            ]);
    }

    public static function getFirstNameField(): TextInput
    {
        return TextInput::make('first_name')
            ->required()
            ->rules(['string', 'max:80']);
    }

    public static function getMiddleNameField(): TextInput
    {
        return TextInput::make('middle_name')
            ->rules(['string', 'max:80']);
    }

    public static function getLastNameField(): TextInput
    {
        return TextInput::make('last_name')
            ->required()
            ->rules(['string', 'max:80']);
    }

    public static function getNicknameField(): TextInput
    {
        return TextInput::make('nickname')
            ->rules(['string', 'max:40']);
    }

    public static function getBirthDateField(): DatePicker
    {
        return DatePicker::make('birth_date')
            ->displayFormat('d M Y')
            ->required()
            ->native(false);
    }

    public static function getGenderField(): ToggleButtons
    {
        return ToggleButtons::make('gender')
            ->required()
            ->options(Gender::class)
            ->inline();
    }

    public static function getRelationshipField(): Select
    {
        return Select::make('relationship')
            ->options(Relationship::class)
            ->required();
    }

    public static function getNotesField(): Textarea
    {
        return Textarea::make('notes');
    }
}
