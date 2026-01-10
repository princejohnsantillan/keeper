<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Guardians\Tables;

use App\AuthUser;
use App\Avatar;
use App\Enums\Relationship as RelationshipEnum;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class GuardiansTable
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
                        SpatieMediaLibraryImageColumn::make('avatar')
                            ->collection('avatar')
                            ->circular()
                            ->defaultImageUrl(fn (Guardian $record): string => Avatar::generateUrl($record->full_name))
                            ->grow(false),

                        Stack::make([
                            TextColumn::make('full_name')
                                ->label('Name')
                                ->weight(FontWeight::Bold)
                                ->icon(fn (Guardian $record) => $record->gender->getIcon())
                                ->iconColor(fn (Guardian $record) => $record->gender->getColor())
                                ->sortable(['first_name', 'last_name']),

                            TextColumn::make('birth_date')
                                ->date('d M Y')
                                ->suffix(fn (Guardian $record): string => ' · '.$record->birth_date->age.' yrs')
                                ->sortable()
                                ->color('gray'),
                        ]),
                    ]),

                    Stack::make([
                        TextColumn::make('email')
                            ->icon(Heroicon::Envelope)
                            ->sortable()
                            ->copyable(),

                        TextColumn::make('phone')
                            ->icon(Heroicon::Phone)
                            ->copyable(),
                    ]),

                    TextColumn::make('children.id')
                        ->label('Children')
                        ->icon(function (string $state, Guardian $record): ?string {
                            $child = $record->children->firstWhere('id', $state);

                            return $child?->gender->getIcon();
                        })
                        ->iconColor(function (string $state, Guardian $record): ?array {
                            $child = $record->children->firstWhere('id', $state);

                            return $child?->gender->getColor();
                        })
                        ->formatStateUsing(function (string $state, Guardian $record): string {
                            $child = $record->children->firstWhere('id', $state);

                            if (! $child) {
                                return '';
                            }

                            /** @var RelationshipEnum|null $relationship */
                            $relationship = $child->pivot->relationship;

                            $inverseLabel = $relationship?->inverse($child->gender)->getLabel() ?? '';
                            $age = $child->birth_date->age;

                            return $child->getNickname().", {$age} yrs".($inverseLabel ? " ({$inverseLabel})" : '');
                        })
                        ->listWithLineBreaks(),
                ])->space(3),
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
                    })->visible(fn (Guardian $record) => AuthUser::guardianId() !== $record->id),
            ]);
    }
}
