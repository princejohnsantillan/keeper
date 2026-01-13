<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Tables;

use App\Filament\Actions\DeleteChildAction;
use App\Filament\Actions\EditChildAction;
use App\Filament\Actions\UpdateChildGuardiansAction;
use App\Filament\Components\Tables\AppSpatieMediaLibraryImageColumn;
use App\Models\Child;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ChildrenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->paginated(false)
            ->persistSortInSession()
            ->defaultSort('first_name', 'asc')
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    Split::make([
                        AppSpatieMediaLibraryImageColumn::avatar()
                            ->imageSize(80)
                            ->grow(false),

                        Stack::make([
                            self::nicknameColumn(),
                            self::birthDateColumn(),
                        ]),
                    ]),
                ])->space(3),
            ])
            ->recordAction('edit')
            ->recordActions([]);
    }

    private static function nicknameColumn(): TextColumn
    {
        return TextColumn::make('nickname')
            ->label('Name')
            ->weight(FontWeight::Bold)
            ->icon(fn (Child $record) => $record->gender->getIcon())
            ->iconColor(fn (Child $record) => $record->gender->getColor())
            ->getStateUsing(fn (Child $record): string => $record->getNickname())
            ->description(fn (Child $record): string => $record->full_name)
            ->sortable(['first_name', 'last_name']);
    }

    private static function birthDateColumn(): TextColumn
    {
        return TextColumn::make('birth_date')
            ->date('d M Y')
            ->suffix(fn (Child $record): string => ' · '.$record->birth_date->age.' yrs')
            ->sortable()
            ->color('gray');
    }

    public static function getEditAction(): EditAction
    {
        return EditChildAction::make();
    }

    public static function getUpdateGuardiansAction(): Action
    {
        return UpdateChildGuardiansAction::make();
    }

    public static function getDeleteAction(): DeleteAction
    {
        return DeleteChildAction::make();
    }
}
