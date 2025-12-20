<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Guardians\Tables;

use App\Models\Guardian;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class GuardiansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->paginated(false)
            ->columns([
                TextColumn::make('first_name')
                    ->sortable(),
                TextColumn::make('middle_name')
                    ->sortable(),
                TextColumn::make('last_name')
                    ->sortable(),
                TextColumn::make('birth_date')
                    ->date('d M Y')
                    ->description(function (Guardian $record): ?string {
                        $age = $record->birth_date?->age;

                        return $age === null ? null : "{$age} yrs";
                    })
                    ->sortable(),
                IconColumn::make('gender')->sortable()->alignCenter(),
                TextColumn::make('email')
                    ->copyable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->copyable()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()->slideOver(),
            ]);

    }
}
