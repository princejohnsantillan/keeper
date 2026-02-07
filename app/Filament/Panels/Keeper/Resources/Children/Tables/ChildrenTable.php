<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Children\Tables;

use App\Filament\Actions\EditOrganizationNoteAction;
use App\Filament\Actions\EditOrganizationTagsAction;
use App\Filament\Actions\KeeperEditChildAction;
use App\Filament\Components\Tables\AppIconColumn;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use App\Filament\Components\Tables\AppTagsColumn;
use App\Filament\Components\Tables\AppTextColumn;
use Filament\Actions\ActionGroup;
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
                self::getEditAction(),
                self::annotateActionGroup(),
            ]);
    }

    public static function getEditAction(): EditAction
    {
        return KeeperEditChildAction::make();
    }

    private static function annotateActionGroup(): ActionGroup
    {
        return ActionGroup::make([
            EditOrganizationTagsAction::make(),
            EditOrganizationNoteAction::make(),
        ])
            ->label('Annotate')
            ->icon('heroicon-o-pencil-square');
    }
}
