<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Resources\Activities\Tables;

use App\Enums\Gender;
use App\Enums\Relationship;
use App\Filament\Panels\Keeper\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\ReadableCode;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

final class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->description(fn (Activity $activity): ?string => $activity->description)
                    ->sortable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->dateTime('D - h:i A')
                    ->description(fn (Activity $record) => $record->starts_at->format('F d'))
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime('D - h:i A')
                    ->description(fn (Activity $record) => $record->ends_at->format('F d'))
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('walk_in')
                    ->slideOver()
                    ->label('Walk-in')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->modalHeading('Walk-in Registration')
                    ->modalDescription(fn (Activity $record): string => "Register a new guardian and child for {$record->title}")
                    ->schema([
                        Fieldset::make('Guardian Details')
                            ->schema([
                                TextInput::make('guardian_first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('guardian_middle_name')
                                    ->label('Middle Name')
                                    ->columnSpan(2),
                                TextInput::make('guardian_last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->columnSpan(2),
                                DatePicker::make('guardian_birth_date')
                                    ->label('Birth Date')
                                    ->displayFormat('d M Y')
                                    ->native(false)
                                    ->columnSpan(3),
                                ToggleButtons::make('guardian_gender')
                                    ->label('Gender')
                                    ->required()
                                    ->options(Gender::class)
                                    ->inline()
                                    ->columnSpan(3),
                                TextInput::make('guardian_email')
                                    ->label('Email')
                                    ->email()
                                    ->columnSpan(3),
                                TextInput::make('guardian_phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->columnSpan(3),
                            ])
                            ->columns(6)
                            ->columnSpanFull(),
                        Fieldset::make('Child Details')
                            ->schema([
                                TextInput::make('child_first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('child_middle_name')
                                    ->label('Middle Name')
                                    ->columnSpan(2),
                                TextInput::make('child_last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('child_nickname')
                                    ->label('Nickname')
                                    ->columnSpan(2),
                                DatePicker::make('child_birth_date')
                                    ->label('Birth Date')
                                    ->required()
                                    ->displayFormat('d M Y')
                                    ->native(false)
                                    ->columnSpan(2),
                                ToggleButtons::make('child_gender')
                                    ->label('Gender')
                                    ->required()
                                    ->options(Gender::class)
                                    ->inline()
                                    ->columnSpan(2),
                                Textarea::make('child_notes')
                                    ->label('Notes')
                                    ->columnSpanFull(),
                            ])
                            ->columns(6)
                            ->columnSpanFull(),
                        Fieldset::make('Relationship')
                            ->schema([
                                Select::make('relationship')
                                    ->label('Relationship to Child')
                                    ->options(Relationship::class)
                                    ->required()
                                    ->native(false)
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
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
                    }),
                Action::make('attendance')
                    ->label('Attendance')
                    ->icon(Heroicon::ClipboardDocumentCheck)
                    ->color('gray')
                    ->url(fn (Activity $record): string => ActivityResource::getUrl('attendance', ['record' => $record])),
                EditAction::make()->slideOver()
                    ->hiddenLabel(),
                DeleteAction::make()->hiddenLabel(),
            ]);
    }
}
