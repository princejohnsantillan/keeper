<?php

declare(strict_types=1);

namespace App\Filament\Keeper\Resources\Activities\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

final class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->required()->columnSpanFull(),
                DateTimePicker::make('starts_at')
                    ->displayFormat('d M Y (h:i A)')
                    ->native(false)
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->displayFormat('d M Y (h:i A)')
                    ->native(false)
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ])->columns(2);
    }
}
