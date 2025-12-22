<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Tables;

use App\AuthUser;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
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
                EditAction::make()
                    ->slideOver()
                    ->hiddenLabel()
                    ->mutateRecordDataUsing(function (array $data, Guardian $record): array {
                        $children = AuthUser::guardian()->children;

                        $relationshipsByChildId = Relationship::query()
                            ->where('guardian_id', $record->id)
                            ->whereIn('child_id', $children->pluck('id'))
                            ->get()
                            ->keyBy('child_id');

                        $data['children'] = $children
                            ->map(fn (Child $child): array => [
                                'child_id' => $child->id,
                                'child_name' => $child->full_name,
                                'relationship' => $relationshipsByChildId->get($child->id)?->relationship?->value,
                            ])
                            ->all();

                        return $data;
                    })
                    ->using(function (Guardian $record, array $data): Guardian {
                        /** @var array<int, array{child_id?: int|string, relationship?: string|null}> $rows */
                        $rows = $data['children'] ?? [];

                        unset($data['children']);

                        $record->update($data);

                        $syncData = [];

                        foreach ($rows as $row) {
                            $childId = (int) ($row['child_id'] ?? 0);
                            $relationship = $row['relationship'] ?? null;

                            if ($childId <= 0) {
                                continue;
                            }

                            if ($relationship === null || $relationship === '') {
                                continue;
                            }

                            $syncData[$childId] = [
                                'relationship' => $relationship,
                            ];
                        }

                        $record->children()->sync($syncData);

                        return $record;
                    }),
                DeleteAction::make()
                    ->hiddenLabel()
                    ->using(function (Guardian $record): void {
                        $childIds = AuthUser::guardian()->children()->pluck('children.id');

                        Relationship::query()
                            ->where('guardian_id', $record->id)
                            ->whereIn('child_id', $childIds)
                            ->delete();

                        Notification::make()
                            ->title('Deleted')
                            ->danger()
                            ->send();
                    }),
            ]);
    }
}
