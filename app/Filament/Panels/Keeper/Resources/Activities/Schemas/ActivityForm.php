<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Schemas;

use App\Filament\Components\Forms\AppDateTimePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppSpatieMediaLibraryFileUpload;
use App\Filament\Components\Forms\AppTextarea;
use App\Filament\Components\Forms\AppTextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

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
                AppDateTimePicker::publishAt(),
                self::isPrivateToggle(),
                AppSelect::term()
                    ->columnSpanFull(),
                self::messageSelect()
                    ->columnSpanFull(),
                AppTextarea::notes()
                    ->columnSpanFull(),
            ])->columns(2);
    }

    private static function isPrivateToggle(): Toggle
    {
        return Toggle::make('is_private')
            ->label('Private Event')
            ->helperText('When enabled, guardians must enter an invitation code to register.')
            ->default(false);
    }

    private static function messageSelect(): Select
    {
        return Select::make('message_id')
            ->label('Gate Pass Message')
            ->relationship(
                name: 'message',
                titleAttribute: 'name',
                modifyQueryUsing: fn (Builder $query): Builder => $query->whereNull('archived_at'),
            )
            ->searchable()
            ->preload()
            ->placeholder('Select a message template...')
            ->helperText('This message will be appended to gate pass emails for this activity.');
    }
}
