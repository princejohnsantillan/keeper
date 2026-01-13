<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
use App\Enums\Relationship as RelationshipEnum;
use App\Filament\Notifications\AppNotification;
use App\Models\Child;
use App\Models\Guardian;
use App\Models\Relationship;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;

final class UpdateChildGuardiansAction
{
    public static function make(?string $name = 'update_guardians', string $label = 'Guardians'): Action
    {
        return Action::make($name)->label($label)
            ->slideOver()
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
                self::guardiansRepeater()
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

                AppNotification::guardiansUpdated()->send();
            });
    }

    private static function guardiansRepeater(): Repeater
    {
        return Repeater::make('guardians')
            ->hiddenLabel()
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->table([
                TableColumn::make('Guardian'),
                TableColumn::make('Relationship'),
            ])
            ->schema([
                self::guardianIdHidden(),
                self::guardianNameInput(),
                self::relationshipSelect(),
            ]);
    }

    private static function guardianIdHidden(): Hidden
    {
        return Hidden::make('guardian_id')
            ->required();
    }

    private static function guardianNameInput(): TextInput
    {
        return TextInput::make('guardian_name')
            ->label('Guardian')
            ->disabled();
    }

    private static function relationshipSelect(): Select
    {
        return Select::make('relationship')
            ->options(RelationshipEnum::class)
            ->placeholder('Select a relationship')
            ->native(false);
    }
}
