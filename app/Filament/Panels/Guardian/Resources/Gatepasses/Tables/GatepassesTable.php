<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Gatepasses\Tables;

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
                AppTextColumn::title('activity.title', 'Activity'),
                self::guardianFullNameColumn(),
                self::childFullNameColumn(),
            ]);
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
