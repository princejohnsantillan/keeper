<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Gatepasses\Tables;

use App\Filament\Actions\CheckInGatepassAction;
use App\Filament\Actions\CheckOutGatepassAction;
use App\Filament\Components\Tables\AppTextColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class GatepassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                AppTextColumn::code(),
                self::activityTitleColumn(),
                self::guardianFullNameColumn(),
                self::childFullNameColumn(),
            ])
            ->recordActions([
                CheckInGatepassAction::make(),
                CheckOutGatepassAction::make(),
            ]);
    }

    private static function activityTitleColumn(): TextColumn
    {
        return TextColumn::make('activity.title')
            ->searchable()
            ->sortable();
    }

    private static function guardianFullNameColumn(): TextColumn
    {
        return TextColumn::make('guardian.full_name')
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query->whereHas('guardian', function (Builder $query) use ($search): void {
                    $query->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%");
                });
            })
            ->sortable(query: function (Builder $query, string $direction): Builder {
                return $query
                    ->join('guardians', 'gatepasses.guardian_id', '=', 'guardians.id')
                    ->orderBy('guardians.first_name', $direction)
                    ->orderBy('guardians.last_name', $direction)
                    ->select('gatepasses.*');
            });
    }

    private static function childFullNameColumn(): TextColumn
    {
        return TextColumn::make('child.full_name')
            ->searchable(query: function (Builder $query, string $search): Builder {
                return $query->whereHas('child', function (Builder $query) use ($search): void {
                    $query->where('first_name', 'ilike', "%{$search}%")
                        ->orWhere('last_name', 'ilike', "%{$search}%");
                });
            })
            ->sortable(query: function (Builder $query, string $direction): Builder {
                return $query
                    ->join('children', 'gatepasses.child_id', '=', 'children.id')
                    ->orderBy('children.first_name', $direction)
                    ->orderBy('children.last_name', $direction)
                    ->select('gatepasses.*');
            });
    }
}
