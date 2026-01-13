<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppTextarea;
use App\Filament\Components\Forms\AppTextInput;
use App\Filament\Components\Forms\AppToggleButtons;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\ReadableCode;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Illuminate\Support\Facades\DB;

final class WalkInAction
{
    public static function make(?string $name = 'walk_in', string $label = 'Walk-in'): Action
    {
        return Action::make($name)->label($label)
            ->slideOver()
            ->icon('heroicon-o-user-plus')
            ->color('success')
            ->modalHeading('Walk-in Registration')
            ->modalDescription(fn (Activity $record): string => "Register a new guardian and child for {$record->title}")
            ->schema([
                self::guardianFieldset(),
                self::childFieldset(),
                self::relationshipFieldset(),
            ])
            ->action(function (array $data, Activity $record): void {
                DB::transaction(function () use ($data, $record): void {
                    $guardian = Guardian::create([
                        'first_name' => $data['guardian_first_name'],
                        'middle_name' => $data['guardian_middle_name'],
                        'last_name' => $data['guardian_last_name'],
                        'birth_date' => $data['guardian_birth_date'],
                        'gender' => $data['guardian_gender'],
                        'email' => $data['guardian_email'],
                        'phone' => $data['guardian_phone'],
                    ]);

                    $child = Child::create([
                        'first_name' => $data['child_first_name'],
                        'middle_name' => $data['child_middle_name'],
                        'last_name' => $data['child_last_name'],
                        'nickname' => $data['child_nickname'],
                        'birth_date' => $data['child_birth_date'],
                        'gender' => $data['child_gender'],
                        'notes' => $data['child_notes'],
                    ]);

                    $guardian->children()->attach($child->id, [
                        'relationship' => $data['relationship'],
                    ]);

                    Gatepass::create([
                        'guardian_id' => $guardian->id,
                        'child_id' => $child->id,
                        'activity_id' => $record->id,
                        'code' => ReadableCode::generate(),
                    ]);
                });

                Notification::make()
                    ->title('Walk-in registered successfully')
                    ->success()
                    ->send();
            });
    }

    private static function guardianFieldset(): Fieldset
    {
        return Fieldset::make('Guardian Details')
            ->schema([
                AppTextInput::firstName('guardian_first_name', 'First Name')
                    ->columnSpan(2),
                AppTextInput::middleName('guardian_middle_name', 'Middle Name')
                    ->columnSpan(2),
                AppTextInput::lastName('guardian_last_name', 'Last Name')
                    ->columnSpan(2),
                AppDatePicker::birthDate('guardian_birth_date', 'Birth Date')
                    ->required(false)
                    ->columnSpan(3),
                AppToggleButtons::gender('guardian_gender', 'Gender')
                    ->columnSpan(3),
                AppTextInput::email('guardian_email', 'Email')
                    ->required(false)
                    ->columnSpan(3),
                AppTextInput::phone('guardian_phone', 'Phone')
                    ->columnSpan(3),
            ])
            ->columns(6)
            ->columnSpanFull();
    }

    private static function childFieldset(): Fieldset
    {
        return Fieldset::make('Child Details')
            ->schema([
                AppTextInput::firstName('child_first_name', 'First Name')
                    ->columnSpan(2),
                AppTextInput::middleName('child_middle_name', 'Middle Name')
                    ->columnSpan(2),
                AppTextInput::lastName('child_last_name', 'Last Name')
                    ->columnSpan(2),
                AppTextInput::nickname('child_nickname', 'Nickname')
                    ->columnSpan(2),
                AppDatePicker::birthDate('child_birth_date', 'Birth Date')
                    ->columnSpan(2),
                AppToggleButtons::gender('child_gender', 'Gender')
                    ->columnSpan(2),
                AppTextarea::notes('child_notes', 'Notes')
                    ->columnSpanFull(),
            ])
            ->columns(6)
            ->columnSpanFull();
    }

    private static function relationshipFieldset(): Fieldset
    {
        return Fieldset::make('Relationship')
            ->schema([
                AppSelect::relationship('relationship', 'Relationship to Child')
                    ->columnSpanFull(),
            ])
            ->columnSpanFull();
    }
}
