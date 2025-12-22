<?php

namespace App\Filament\Panels\Keeper\Resources\Gatepasses\Schemas;

use App\AuthUser;
use App\ReadableCode;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class GatepassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->default(ReadableCode::generate())
                    ->disabled()
                    ->dehydrated()
                    ->copyable()
                    ->required(),

                Select::make('activity_id')
                    ->relationship('activity', 'title')
                    ->required(),

                Select::make('guardian_id')
                    ->relationship(
                        name: 'guardian',
                        titleAttribute: 'full_name',

                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->full_name)
                    ->required(),

                Select::make('child_id')
                    ->relationship(
                        name: 'child',
                        titleAttribute: 'first_name',
                        modifyQueryUsing: fn (Builder $query): Builder => $query
                            ->whereHas('relationships', fn (Builder $q) => $q
                                ->where('guardian_id', AuthUser::guardianId())
                            ),
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => $record->full_name)
                    ->required(),
            ])->columns(2);
    }
}
