<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Gatepasses\Tables;

use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class GatepassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')
                    ->badge()
                    ->copyable()
                    ->size(TextSize::Large)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('activity.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guardian.full_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('child.full_name')
                    ->searchable()
                    ->sortable(),
            ]);
    }
}
