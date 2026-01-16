<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Schemas;

use App\Filament\Components\Forms\AppDateTimePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppSpatieMediaLibraryFileUpload;
use App\Filament\Components\Forms\AppTextarea;
use App\Filament\Components\Forms\AppTextInput;
use Filament\Forms\Components\Select;
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
                self::messageSelect()
                    ->columnSpanFull(),
                AppTextarea::notes()
                    ->columnSpanFull(),
            ])->columns(2);
    }

    private static function messageSelect(): Select
    {
        return Select::make('message_id')
            ->label('Gate Pass Message')
            ->relationship('message', 'name')
            ->searchable()
            ->preload()
            ->placeholder('Select a message template...')
            ->helperText('This message will be appended to gate pass emails for this activity.');
    }
}
