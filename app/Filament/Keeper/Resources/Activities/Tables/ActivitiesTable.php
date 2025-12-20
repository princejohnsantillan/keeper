<?php

declare(strict_types=1);

namespace App\Filament\Keeper\Resources\Activities\Tables;

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
                TextColumn::make('title')
                    ->description(fn (Activity $activity): ?string => $activity->description)
                    ->sortable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->dateTime('D - h:i A')
                    ->description(fn (Activity $record) => $record->starts_at->format('F d'))
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime('D - h:i A')
                    ->description(fn (Activity $record) => $record->ends_at->format('F d'))
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ]);
    }
}
