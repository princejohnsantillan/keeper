<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Guardians\Tables;

use App\Filament\Components\Tables\AppIconColumn;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use App\Filament\Components\Tables\AppTagsColumn;
use App\Filament\Components\Tables\AppTextColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class GuardiansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppSpatieMediaLibraryImageColumn::avatar(),
                AppTextColumn::firstName(),
                AppTextColumn::middleName(),
                AppTextColumn::lastName(),
                self::birthDateColumn(),
                AppIconColumn::gender(),
                AppTextColumn::email('email', 'Email address'),
                AppTextColumn::phone(),
                AppTagsColumn::tags(),
            ])
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    private static function birthDateColumn(): TextColumn
    {
        return TextColumn::make('birth_date')
            ->date()
            ->sortable();
    }
}
