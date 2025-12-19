<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Children\Tables;

use App\Enums\Relationship as RelationshipEnum;
use App\Models\Child;
use App\Models\Relationship;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

final class ChildrenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ChildrenTable::getFirstNameColumn(),
                ChildrenTable::getMiddleNameColumn(),
                ChildrenTable::getLastNameColumn(),
                ChildrenTable::getNicknameColumn(),
                ChildrenTable::getBirthDateColumn(),
                ChildrenTable::getGenderColumn()->alignCenter(),
                ChildrenTable::getRelationshipColumn()->alignCenter(),
            ])
            ->filters([
                ChildrenTable::getGenderFilter(),
            ])
            ->recordActions([
                ChildrenTable::getEditAction(),
            ])
            ->recordAction(null)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getFirstNameColumn(): TextColumn
    {
        return TextColumn::make('first_name')
            ->searchable()
            ->sortable();
    }

    public static function getMiddleNameColumn(): TextColumn
    {
        return TextColumn::make('middle_name')
            ->searchable()
            ->sortable();
    }

    public static function getLastNameColumn(): TextColumn
    {
        return TextColumn::make('last_name')
            ->searchable()
            ->sortable();
    }

    public static function getNicknameColumn(): TextColumn
    {
        return TextColumn::make('nickname')
            ->searchable()
            ->sortable();
    }

    public static function getBirthDateColumn(): TextColumn
    {
        return TextColumn::make('birth_date')
            ->date()
            ->description(function (Child $record): string {
                $age = $record->birth_date->age;

                return "{$age} years old";
            })
            ->sortable();
    }

    public static function getGenderColumn(): IconColumn
    {
        return IconColumn::make('gender');
    }

    public static function getRelationshipColumn(): TextColumn
    {
        return TextColumn::make('guardian_relationship')
            ->label('Relationship')
            ->getStateUsing(function (Child $record): ?string {
                $relationship = Relationship::where('child_id', $record->id)
                    ->whereNotNull('guardian_id')
                    ->where('guardian_id', Auth::user()?->guardian?->id)
                    ->first();

                return $relationship?->relationship?->value;
            })
            ->formatStateUsing(fn (?string $state): ?string => $state ? RelationshipEnum::from($state)->name : null)
            ->badge()
            ->size(TextSize::Large)
            ->color(Color::Stone);
    }

    public static function getGenderFilter(): SelectFilter
    {
        return SelectFilter::make('gender')
            ->options([
                1 => 'Male',
                0 => 'Female',
            ]);
    }

    public static function getEditAction(): EditAction
    {
        return EditAction::make()
            ->hiddenLabel()
            ->slideOver()
            ->mutateRecordDataUsing(function (array $data, Child $record): array {
                $relationship = Relationship::where('child_id', $record->id)
                    ->whereNotNull('guardian_id')
                    ->where('guardian_id', Auth::user()?->guardian?->id)
                    ->first();

                $data['relationship'] = $relationship?->relationship?->value;

                return $data;
            })
            ->using(function (Child $record, array $data): Child {

                $relationship = $data['relationship'];

                unset($data['relationship']);

                $record->update($data);

                Relationship::where('child_id', $record->id)
                    ->where('guardian_id', Auth::user()?->guardian?->id)
                    ->whereNotNull('guardian_id')
                    ->update(['relationship' => $relationship]);

                return $record;
            });
    }
}
