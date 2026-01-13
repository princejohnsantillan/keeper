<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Tables;

use App\Filament\Actions\ViewAttendanceAction;
use App\Filament\Actions\WalkInAction;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use App\Filament\Components\Tables\AppTextColumn;
use App\Models\Activity;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppSpatieMediaLibraryImageColumn::thumbnail(),
                self::titleColumn(),
                AppTextColumn::location(),
                self::startsAtColumn(),
                self::endsAtColumn(),
                self::creatorColumn(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                WalkInAction::make(),
                ViewAttendanceAction::make(),
                EditAction::make()->slideOver()
                    ->hiddenLabel(),
                DeleteAction::make()->hiddenLabel(),
            ]);
    }

    private static function titleColumn(): TextColumn
    {
        return TextColumn::make('title')
            ->description(fn (Activity $activity): ?string => $activity->description)
            ->sortable();
    }

    private static function startsAtColumn(): TextColumn
    {
        return TextColumn::make('starts_at')
            ->dateTime('D - h:i A')
            ->description(fn (Activity $record) => $record->starts_at->format('F d'))
            ->sortable();
    }

    private static function endsAtColumn(): TextColumn
    {
        return TextColumn::make('ends_at')
            ->dateTime('D - h:i A')
            ->description(fn (Activity $record) => $record->ends_at->format('F d'))
            ->sortable();
    }

    private static function creatorColumn(): TextColumn
    {
        return TextColumn::make('creator.name')
            ->numeric()
            ->sortable();
    }
}
