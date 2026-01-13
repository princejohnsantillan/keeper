<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Children\Tables;

use App\AuthUser;
use App\Avatar;
use App\Enums\Relationship as RelationshipEnum;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                        SpatieMediaLibraryImageColumn::make('avatar')
                            ->collection('avatar')
                            ->circular()
                            ->imageSize(80)
                            ->defaultImageUrl(fn (Child $record): string => Avatar::generateUrl($record->full_name))
                            ->grow(false),

                        Stack::make([
                            TextColumn::make('nickname')
                                ->label('Name')
                                ->weight(FontWeight::Bold)
                                ->icon(fn (Child $record) => $record->gender->getIcon())
                                ->iconColor(fn (Child $record) => $record->gender->getColor())
                                ->getStateUsing(fn (Child $record): string => $record->getNickname())
                                ->description(fn (Child $record): string => $record->full_name)
                                ->sortable(['first_name', 'last_name']),

                            TextColumn::make('birth_date')
                                ->date('d M Y')
                                ->suffix(fn (Child $record): string => ' · '.$record->birth_date->age.' yrs')
                                ->sortable()
                                ->color('gray'),
                        ]),
                    ]),
                ])->space(3),
            ])
            ->recordAction('edit')
            ->recordActions([]);
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
            });
    }

    public static function getGenderColumn(): IconColumn
    {
        return IconColumn::make('gender')->label('');
    }

    public static function getRelationshipColumn(): TextColumn
    {
        return TextColumn::make('relationship')
            ->getStateUsing(function (Child $record): ?string {
                $relationship = Relationship::where('child_id', $record->id)
                    ->whereNotNull('guardian_id')
                    ->where('guardian_id', AuthUser::guardianId())
                    ->first();

                return $relationship?->relationship->inverse($record->gender)->getLabel();
            })
            ->badge()
            ->size(TextSize::Large)
            ->color(Color::Stone);
    }

    public static function getEditAction(): EditAction
    {
        return EditAction::make()
//            ->hiddenLabel()
            ->slideOver()
            ->mutateRecordDataUsing(function (array $data, Child $record): array {
                $relationship = Relationship::where('child_id', $record->id)
                    ->whereNotNull('guardian_id')
                    ->where('guardian_id', AuthUser::guardianId())
                    ->first();

                $data['relationship'] = $relationship?->relationship?->value;

                return $data;
            })
            ->using(function (Child $record, array $data): Child {
                $relationship = $data['relationship'];

                unset($data['relationship']);

                $record->update($data);

                Relationship::where('child_id', $record->id)
                    ->where('guardian_id', AuthUser::guardianId())
                    ->whereNotNull('guardian_id')
                    ->update(['relationship' => $relationship]);

                return $record;
            });
    }

    public static function getUpdateGuardiansAction(): Action
    {
        return Action::make('update_guardians')
            ->slideOver()
            ->label('Guardians')
            ->icon('entypo-shield')
            ->modalHeading(fn (Child $record): string => "Guardians of {$record->getNickname()}")
            ->modalSubmitActionLabel('Save changes')
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
                            ->label('Guardian')
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

    public static function getDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
//            ->hiddenLabel()
            ->using(function (Child $record) {
                Relationship::where('child_id', $record->id)
                    ->where('guardian_id', AuthUser::guardianId())
                    ->delete();

                Notification::make()
                    ->title('Deleted')
                    ->danger()
                    ->send();
            });
    }
}
