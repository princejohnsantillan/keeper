<?php

declare(strict_types=1);

namespace App\Filament\Guardian\Resources\Activities\Tables;

use App\AuthUser;
use App\Models\Activity;
use App\Models\Gatepass;
use App\ReadableCode;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

final class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->paginated(false)
            ->defaultSort('starts_at', 'asc')
            ->columns([
                TextColumn::make('title')
                    ->description(fn (Activity $activity): ?string => $activity->description)
                    ->sortable(),
                TextColumn::make('location')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->dateTime('D - h:i A')
                    ->description(fn (Activity $record) => $record->starts_at->format('F d'))
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime('D - h:i A')
                    ->description(fn (Activity $record) => $record->ends_at->format('F d'))
                    ->sortable(),
                TextColumn::make('organization.name')
                    ->label('Organized by'),

            ])
            ->recordActions([
                Action::make('add_gatepass')
                    ->icon('entypo-lock')
                    ->label('Gatepass')
                    ->fillForm(function (): array {
                        $guardian = AuthUser::guardian();
                        $children = $guardian->children()->with('guardians')->get();

                        return [
                            'children' => $children->map(fn ($child) => [
                                'child_id' => $child->id,
                                'child_name' => $child->full_name,
                                'guardian_id' => $guardian->id,
                            ])->toArray(),
                        ];
                    })
                    ->schema([
                        Repeater::make('children')
                            ->hiddenLabel()
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                Hidden::make('child_id'),
                                TextInput::make('child_name')
                                    ->label('Child')
                                    ->disabled()
                                    ->dehydrated(false),
                                Select::make('guardian_id')
                                    ->label('Guardian for Check-in/out')
                                    ->options(function (callable $get): array {
                                        $childId = $get('child_id');

                                        if (! $childId) {
                                            return [];
                                        }

                                        $child = \App\Models\Child::with('guardians')->find($childId);

                                        if (! $child) {
                                            return [];
                                        }

                                        return $child->guardians->pluck('full_name', 'id')->toArray();
                                    })
                                    ->required(),
                            ])
                            ->columns(2),
                    ])
                    ->modalSubmitActionLabel('Create gatepass')
                    ->action(function (array $data, Activity $record): void {
                        $createdCount = 0;

                        foreach ($data['children'] as $childData) {
                            $childId = $childData['child_id'];
                            $guardianId = $childData['guardian_id'];

                            // Generate a unique code for this activity
                            do {
                                $code = ReadableCode::generate();
                            } while (Gatepass::query()
                                ->where('activity_id', $record->id)
                                ->where('code', $code)
                                ->exists());

                            Gatepass::query()->create([
                                'child_id' => $childId,
                                'guardian_id' => $guardianId,
                                'activity_id' => $record->id,
                                'code' => $code,
                            ]);

                            $createdCount++;
                        }

                        Notification::make()
                            ->title('Gatepass Created')
                            ->body("Successfully created {$createdCount} gatepass(es) for {$record->title}.")
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
