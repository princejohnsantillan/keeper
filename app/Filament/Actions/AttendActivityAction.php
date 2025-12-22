<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\ReadableCode;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

final class AttendActivityAction
{
    public static function make(?string $name = 'attend_activity', string $label = 'Attend'): Action
    {
        return Action::make($name)->label($label)
            ->slideOver()
            ->button()
            ->modalSubmitActionLabel('Attend')
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

                                if (blank($childId)) {
                                    return [];
                                }

                                $child = Child::query()->with('guardians')->find($childId);

                                if ($child === null) {
                                    return [];
                                }

                                return $child->guardians->pluck('full_name', 'id')->toArray();
                            })
                            ->required(),
                    ])
                    ->columns(2),
            ])
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
            ->action(function (array $data, Activity $record): void {
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
                }
            })
            ->successNotification(
                AppNotification::registeredToActivity()
            );
    }
}
