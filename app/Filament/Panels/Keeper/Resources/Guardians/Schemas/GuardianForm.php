<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Guardians\Schemas;

use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppSpatieMediaLibraryFileUpload;
use App\Filament\Components\Forms\AppTagsSelect;
use App\Filament\Components\Forms\AppTextInput;
use Filament\Schemas\Schema;

final class GuardianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppSpatieMediaLibraryFileUpload::avatar()
                    ->columnSpanFull(),
                AppTextInput::firstName(),
                AppTextInput::middleName(),
                AppTextInput::lastName(),
                AppDatePicker::birthDate()
                    ->maxDate(now()->subYears(18)),
                AppSelect::gender(),
                AppTextInput::email(),
                AppTextInput::phone(),
                AppTagsSelect::tags()
                    ->columnSpanFull(),
            ]);
    }
}
