<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Keepers\Tables;

use App\Filament\Components\Tables\AppTextColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class KeepersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppTextColumn::fullName()
                    ->state(fn ($record): string => $record->user->name),

                AppTextColumn::email()
                    ->state(fn ($record): string => $record->user->email),

                TextColumn::make('role')
                    ->badge()
                    ->sortable(),

                AppTextColumn::createdAt(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
