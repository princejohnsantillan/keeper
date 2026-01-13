<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Schemas;

use App\Filament\Components\Forms\AppDateTimePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppSpatieMediaLibraryFileUpload;
use App\Filament\Components\Forms\AppTextarea;
use App\Filament\Components\Forms\AppTextInput;
use Filament\Schemas\Schema;

final class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                AppSpatieMediaLibraryFileUpload::thumbnail()
                    ->columnSpanFull(),
                AppTextInput::title()
                    ->columnSpanFull(),
                AppTextarea::description()
                    ->columnSpanFull(),
                AppTextInput::location()
                    ->columnSpanFull(),
                AppDateTimePicker::startsAt(),
                AppDateTimePicker::endsAt(),
                AppDateTimePicker::publishedAt(),
                AppSelect::term()
                    ->columnSpanFull(),
                AppTextarea::notes()
                    ->columnSpanFull(),
            ])->columns(2);
    }
}
