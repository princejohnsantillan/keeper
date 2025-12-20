<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Children\Tables;

use App\Models\Child;
use App\Models\Relationship;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

final class ChildrenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->paginated(false)
            ->columns([
                ChildrenTable::getFirstNameColumn(),
                ChildrenTable::getMiddleNameColumn(),
                ChildrenTable::getLastNameColumn(),
                ChildrenTable::getNicknameColumn(),
                ChildrenTable::getBirthDateColumn(),
                ChildrenTable::getGenderColumn()->alignCenter(),
                ChildrenTable::getRelationshipColumn()->alignCenter(),
            ])
            ->recordActions([
                ChildrenTable::getEditAction(),
                ChildrenTable::getDeleteAction(),
                ViewAction::make()->hiddenLabel(),
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
            ->date('d M Y')
            ->description(function (Child $record): string {
                $age = $record->birth_date->age;

                return "{$age} yrs";
            })
            ->sortable();
    }

    public static function getGenderColumn(): IconColumn
    {
        return IconColumn::make('gender');
    }

    public static function getRelationshipColumn(): TextColumn
    {
        return TextColumn::make('relationship')
            ->getStateUsing(function (Child $record): ?string {
                $relationship = Relationship::where('child_id', $record->id)
                    ->whereNotNull('guardian_id')
                    ->where('guardian_id', Auth::user()?->guardian?->id)
                    ->first();

                return $relationship?->relationship?->getLabel();
            })
            ->badge()
            ->size(TextSize::Large)
            ->color(Color::Stone);
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

    private static function getDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->hiddenLabel()
            ->using(function (Child $record) {
                Relationship::where('child_id', $record->id)
                    ->where('guardian_id', Auth::user()?->guardian?->id)
                    ->delete();

                Notification::make()
                    ->title('Deleted')
                    ->danger()
                    ->send();
            });
    }
}
