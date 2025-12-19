<?php

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

class ChildrenTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('middle_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nickname')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('birth_date')
                    ->date()
                    ->description(function (Child $record): ?string {
                        $age = $record->birth_date->age;

                        return "{$age} years old";
                    })
                    ->sortable(),
                IconColumn::make('gender')->alignCenter(),
                TextColumn::make('guardian_relationship')
                    ->label('Relationship')
                    ->getStateUsing(function (Child $record): ?string {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();

                        $relationship = Relationship::where('child_id', $record->id)
                            ->where('guardian_id', $user->guardian->id)
                            ->first();

                        return $relationship?->relationship?->value;
                    })
                    ->formatStateUsing(fn (?string $state): ?string => $state ? RelationshipEnum::from($state)->name : null)
                    ->badge()
                    ->size(TextSize::Large)
                    ->color(Color::Stone)
                    ->alignCenter(),
            ])
            ->recordActions([
                EditAction::make()
                    ->hiddenLabel()

                    ->slideOver()
                    ->mutateRecordDataUsing(function (array $data, Child $record): array {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();

                        $relationship = Relationship::where('child_id', $record->id)
                            ->where('guardian_id', $user->guardian->id)
                            ->first();

                        $data['relationship'] = $relationship?->relationship?->value;

                        return $data;
                    })
                    ->using(function (Child $record, array $data): Child {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();

                        $relationshipType = $data['relationship'];
                        unset($data['relationship']);

                        $record->update($data);

                        Relationship::where('child_id', $record->id)
                            ->where('guardian_id', $user->guardian->id)
                            ->update(['relationship' => $relationshipType]);

                        return $record;
                    }),
            ])
            ->recordAction(null)
            ->filters([
                SelectFilter::make('gender')
                    ->options([
                        1 => 'Male',
                        0 => 'Female',
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
