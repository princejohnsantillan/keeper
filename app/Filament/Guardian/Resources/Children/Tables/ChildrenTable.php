<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Children\Tables;

use App\AuthUser;
use App\Enums\Relationship as RelationshipEnum;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                ChildrenTable::getUpdateGuardiansAction(),
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

    private static function getUpdateGuardiansAction(): Action
    {
        return Action::make('update_guardians')
            ->slideOver()
            ->label('Guardians')
            ->icon('entypo-shield')
            ->modalHeading(fn (Child $record): string => "Guardians of {$record->full_name}")
            ->modalSubmitActionLabel('Set')
            ->fillForm(function (Child $record): array {
                $guardians = Guardian::query()
                    ->whereHas('relationships', function (Builder $query): void {
                        $query->whereIn('child_id', AuthUser::guardian()->children()->pluck('children.id'));
                    })->get();

                $relationshipsByGuardianId = Relationship::query()
                    ->where('child_id', $record->id)
                    ->whereIn('guardian_id', $guardians->pluck('id'))
                    ->get()
                    ->keyBy('guardian_id');

                return [
                    'guardians' => $guardians
                        ->map(fn (Guardian $guardian): array => [
                            'guardian_id' => $guardian->id,
                            'guardian_name' => $guardian->full_name,
                            'relationship' => $relationshipsByGuardianId->get($guardian->id)?->relationship?->value,
                        ])
                        ->all(),
                ];
            })
            ->schema([
                Repeater::make('guardians')
                    ->hiddenLabel()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->table([
                        TableColumn::make('Guardian'),
                        TableColumn::make('Relationship'),
                    ])
                    ->schema([
                        Hidden::make('guardian_id')
                            ->required(),
                        TextInput::make('guardian_name')
                            ->disabled(),
                        Select::make('relationship')
                            ->options(RelationshipEnum::class)
                            ->placeholder('Select a relationship')
                            ->native(false),
                    ])
                    ->columnSpanFull(),
            ])
            ->action(function (Child $record, array $data): void {
                /** @var array<int, array{guardian_id?: int|string, relationship?: string|null}> $rows */
                $rows = $data['guardians'] ?? [];

                $syncData = [];

                foreach ($rows as $row) {
                    $guardianId = (int) ($row['guardian_id'] ?? 0);
                    $relationship = $row['relationship'] ?? null;

                    if ($guardianId <= 0) {
                        continue;
                    }

                    if ($relationship === null || $relationship === '') {
                        continue;
                    }

                    $syncData[$guardianId] = [
                        'relationship' => $relationship,
                    ];
                }

                $record->guardians()->sync($syncData);

                Notification::make()
                    ->title('Guardians updated')
                    ->success()
                    ->send();
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
