<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Tables;

use App\Avatar;
use App\Models\Child;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ChildrenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')
                    ->collection('avatar')
                    ->circular()
                    ->defaultImageUrl(fn (Child $record): string => Avatar::generateUrl($record->full_name)),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('middle_name')
                    ->searchable(),
                TextColumn::make('last_name')
                    ->searchable(),
                TextColumn::make('nickname')
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->date('d M Y')
                    ->description(function (Child $record): string {
                        $age = $record->birth_date->age;

                        return "{$age} yrs";
                    })
                    ->sortable(),
                IconColumn::make('gender')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
