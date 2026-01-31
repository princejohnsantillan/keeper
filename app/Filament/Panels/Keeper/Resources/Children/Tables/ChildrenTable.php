<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Tables;

use App\Filament\Components\Tables\AppIconColumn;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use App\Filament\Components\Tables\AppTagsColumn;
use App\Filament\Components\Tables\AppTextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;

final class ChildrenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppSpatieMediaLibraryImageColumn::avatar(),
                AppTextColumn::firstName(),
                AppTextColumn::middleName(),
                AppTextColumn::lastName(),
                AppTextColumn::nickname(),
                AppTextColumn::birthDate(),
                AppIconColumn::gender(),
                AppTagsColumn::tags(),
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
