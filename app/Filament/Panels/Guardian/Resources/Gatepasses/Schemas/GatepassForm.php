<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Gatepasses\Schemas;

use App\AuthUser;
use App\ReadableCode;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

final class GatepassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::code(),
                self::activitySelect(),
                self::guardianSelect(),
                self::childSelect(),
            ])->columns(2);
    }

    private static function code(): TextInput
    {
        return TextInput::make('code')
            ->default(ReadableCode::generate())
            ->disabled()
            ->dehydrated()
            ->copyable()
            ->required();
    }

    private static function activitySelect(): Select
    {
        return Select::make('activity_id')
            ->relationship('activity', 'title')
            ->required();
    }

    private static function guardianSelect(): Select
    {
        return Select::make('guardian_id')
            ->relationship(
                name: 'guardian',
                titleAttribute: 'first_name',
                modifyQueryUsing: fn (Builder $query): Builder => $query
                    ->whereHas('relationships', fn (Builder $q) => $q
                        ->whereIn('child_id', AuthUser::guardian()->children()->pluck('children.id'))
                    ),
            )
            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->full_name)
            ->required();
    }

    private static function childSelect(): Select
    {
        return Select::make('child_id')
            ->relationship(
                name: 'child',
                titleAttribute: 'first_name',
                modifyQueryUsing: fn (Builder $query): Builder => $query
                    ->whereHas('relationships', fn (Builder $q) => $q
                        ->where('guardian_id', AuthUser::guardianId())
                    ),
            )
            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->full_name)
            ->required();
    }
}
