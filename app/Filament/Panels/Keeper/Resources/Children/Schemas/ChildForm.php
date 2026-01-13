<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Schemas;

use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppSpatieMediaLibraryFileUpload;
use App\Filament\Components\Forms\AppSpatieTagsInput;
use App\Filament\Components\Forms\AppTextarea;
use App\Filament\Components\Forms\AppTextInput;
use Filament\Schemas\Schema;

final class ChildForm
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
                AppTextInput::nickname(),
                AppDatePicker::birthDate(),
                AppSelect::gender(),
                AppSpatieTagsInput::tags()
                    ->columnSpanFull(),
                AppTextarea::notes()
                    ->columnSpanFull(),
            ]);
    }
}
