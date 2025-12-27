<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Schemas;

use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppTextarea;
use App\Filament\Components\Forms\AppTextInput;
use App\Filament\Components\Forms\AppToggleButtons;
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
                AppSelect::relationship(),
                AppTextarea::notes()
                    ->columnSpanFull(),
            ]);
    }
}
