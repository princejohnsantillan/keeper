<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Activities\Tables;

use App\Models\Activity;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('starts_at', 'asc')
            ->columns([
                TextColumn::make('title')
                    ->description(fn (Activity $activity): ?string => $activity->description)
                    ->sortable(),
                TextColumn::make('location')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime('D - h:i A')
                    ->description(fn (Activity $record) => $record->starts_at->format('F d'))
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime('D - h:i A')
                    ->description(fn (Activity $record) => $record->ends_at->format('F d'))
                    ->sortable(),
                TextColumn::make('organization.name')
                    ->label('Organized by'),

            ])
            ->recordActions([
                Action::make('gatepass')
                    ->icon('entypo-lock')
                    ->label('Create gatepass'),
            ]);
    }
}
