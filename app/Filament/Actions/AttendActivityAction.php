<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\TermAcceptance;
use App\ReadableCode;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

final class AttendActivityAction
{
    public static function make(?string $name = 'attend_activity', string $label = 'Attend'): Action
    {
        return Action::make($name)->label($label)
            ->slideOver()
            ->button()
            ->fillForm(function (Activity $record): array {
                $guardian = AuthUser::guardian();
                $children = $guardian->children()->with('guardians')->get();

                $existingGatepasses = Gatepass::query()
                    ->where('activity_id', $record->id)
                    ->whereIn('child_id', $children->pluck('id'))
                    ->get()
                    ->keyBy('child_id');

                $termAcceptance = null;
                $hasGatepassWithAcceptance = false;

                if ($record->term !== null) {
                    $termAcceptance = TermAcceptance::query()
                        ->where('term_id', $record->term->id)
                        ->where('guardian_id', $guardian->id)
                        ->first();

                    if ($termAcceptance !== null) {
                        $hasGatepassWithAcceptance = Gatepass::query()
                            ->where('term_acceptance_id', $termAcceptance->id)
                            ->exists();
                    }
                }

                return [
                    'agree_to_terms' => $termAcceptance !== null,
                    'terms_locked' => $hasGatepassWithAcceptance,
                    'children' => $children->map(fn ($child) => [
                        'child_id' => $child->id,
                        'child_name' => $child->full_name,
                        'guardian_id' => $existingGatepasses->get($child->id)?->guardian_id ?? $guardian->id,
                        'gatepass_code' => $existingGatepasses->get($child->id)?->code,
                    ])->toArray(),
                ];
            })
            ->schema(fn (Activity $record): array => array_filter([
                $record->term !== null ? Fieldset::make('Terms and Conditions')
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        Placeholder::make('terms_content')
                            ->hiddenLabel()
                            ->state(new HtmlString(
                                '<div class="fi-prose prose dark:prose-invert max-w-none max-h-64 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">'.
                                Str::markdown($record->term->content).
                                '</div>'
                            ))
                            ->html(),
                        Hidden::make('terms_locked'),
                        Checkbox::make('agree_to_terms')
                            ->label('I have read and agree to the terms and conditions')
                            ->disabled(fn (callable $get): bool => (bool) $get('terms_locked'))
                            ->live()
                            ->afterStateUpdated(function (bool $state, Activity $record): void {
                                $guardian = AuthUser::guardian();
                                $term = $record->term;

                                if ($term === null) {
                                    return;
                                }

                                if ($state) {
                                    TermAcceptance::query()->firstOrCreate(
                                        [
                                            'term_id' => $term->id,
                                            'guardian_id' => $guardian->id,
                                        ],
                                        [
                                            'ip_address' => request()->ip(),
                                            'user_agent' => request()->userAgent(),
                                        ]
                                    );
                                } else {
                                    $termAcceptance = TermAcceptance::query()
                                        ->where('term_id', $term->id)
                                        ->where('guardian_id', $guardian->id)
                                        ->first();

                                    if ($termAcceptance !== null) {
                                        $hasGatepassUsing = Gatepass::query()
                                            ->where('term_acceptance_id', $termAcceptance->id)
                                            ->exists();

                                        if (! $hasGatepassUsing) {
                                            $termAcceptance->delete();
                                        }
                                    }
                                }
                            }),
                    ]) : null,
                Repeater::make('children')
                    ->hiddenLabel()
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->table([
                        TableColumn::make('Child'),
                        TableColumn::make('Guardian for Check-in/out'),
                        TableColumn::make('Gate Pass'),
                    ])
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

                                if (! is_int($childId)) {
                                    return [];
                                }

                                $child = Child::query()->with('guardians')->find($childId);

                                if ($child === null) {
                                    return [];
                                }

                                return $child->guardians->pluck('full_name', 'id')->toArray();
                            })
                            ->disabled(fn (callable $get): bool => ! empty($get('gatepass_code')))
                            ->required(),
                        Flex::make([
                            Hidden::make('gatepass_code'),
                            Action::make('requestGatePass')
                                ->label('Request')
                                ->button()
                                ->color('primary')
                                ->icon(Heroicon::Ticket)
                                ->hidden(fn (callable $get): bool => ! empty($get('gatepass_code')))
                                ->action(function (callable $get, callable $set, Activity $record): void {
                                    if ($record->term !== null && ! $get('../../agree_to_terms')) {
                                        AppNotification::termsNotAgreed()->send();

                                        return;
                                    }

                                    $childId = $get('child_id');
                                    $guardianId = $get('guardian_id');

                                    if (empty($guardianId)) {
                                        return;
                                    }

                                    $guardian = AuthUser::guardian();
                                    $termAcceptanceId = null;

                                    if ($record->term !== null) {
                                        $termAcceptance = TermAcceptance::query()
                                            ->where('term_id', $record->term->id)
                                            ->where('guardian_id', $guardian->id)
                                            ->first();

                                        $termAcceptanceId = $termAcceptance?->id;
                                    }

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
                                        'term_acceptance_id' => $termAcceptanceId,
                                    ]);

                                    $set('gatepass_code', $code);
                                    $set('../../terms_locked', true);

                                    AppNotification::registeredToActivity()->send();
                                }),
                            Placeholder::make('gatepass_display')
                                ->label('Gate Pass')
                                ->hiddenLabel()
                                ->content(fn (callable $get): ?string => $get('gatepass_code'))
                                ->hidden(fn (callable $get): bool => empty($get('gatepass_code'))),
                        ]),
                    ]),
            ]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }
}
