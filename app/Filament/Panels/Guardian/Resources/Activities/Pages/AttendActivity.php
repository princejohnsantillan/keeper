<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities\Pages;

use App\AuthUser;
use App\Filament\Notifications\AppNotification;
use App\Filament\Panels\Guardian\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Services\Contracts\GatepassServiceInterface;
use App\Services\Contracts\TermAcceptanceServiceInterface;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * @property-read Schema $form
 */
final class AttendActivity extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ActivityResource::class;

    protected string $view = 'filament.guardian.pages.attend-activity';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function getTitle(): string
    {
        /** @var Activity $activity */
        $activity = $this->getRecord();

        return $activity->title;
    }

    public function getSubheading(): string
    {
        /** @var Activity $activity */
        $activity = $this->getRecord();

        $schedule = $activity->starts_at->format('F j, Y \a\t g:i A').' - '.$activity->ends_at->format('F j, Y \a\t g:i A');

        return $activity->location.' · '.$schedule;
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_activities')
                ->label('Back to Activities')
                ->icon(Heroicon::ArrowLeft)
                ->color('gray')
                ->url(ActivityResource::getUrl('index')),
        ];
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->fillForm();
    }

    protected function fillForm(): void
    {
        /** @var Activity $activity */
        $activity = $this->getRecord();
        $guardian = AuthUser::guardian();
        $children = $guardian->children()->with('guardians')->get();

        $existingGatepasses = Gatepass::query()
            ->where('activity_id', $activity->id)
            ->whereIn('child_id', $children->pluck('id'))
            ->get()
            ->keyBy('child_id');

        $termAcceptanceService = app(TermAcceptanceServiceInterface::class);

        $hasAcceptance = false;
        $isLocked = false;

        if ($activity->term !== null) {
            $termAcceptance = $termAcceptanceService->getAcceptance($activity->term, $guardian);
            $hasAcceptance = $termAcceptance !== null;
            $isLocked = $termAcceptance !== null && $termAcceptanceService->isLocked($termAcceptance);
        }

        $this->form->fill([
            'agree_to_terms' => $hasAcceptance,
            'terms_locked' => $isLocked,
            'children' => $children->map(fn ($child) => [
                'child_id' => $child->id,
                'child_name' => $child->full_name,
                'guardian_id' => $existingGatepasses->get($child->id)->guardian_id ?? $guardian->id,
                'gatepass_code' => $existingGatepasses->get($child->id)?->code,
            ])->toArray(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        /** @var Activity $activity */
        $activity = $this->getRecord();

        return $schema
            ->components([
                Form::make(array_filter([
                    $activity->term !== null ? Fieldset::make('Terms and Conditions')
                        ->columnSpanFull()
                        ->columns(1)
                        ->schema([
                            Placeholder::make('terms_content')
                                ->hiddenLabel()
                                ->state(new HtmlString(
                                    '<div class="fi-prose prose dark:prose-invert max-w-none max-h-64 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">'.
                                    Str::markdown($activity->term->content).
                                    '</div>'
                                ))
                                ->html(),
                            Hidden::make('terms_locked'),
                            Checkbox::make('agree_to_terms')
                                ->label('I have read and agree to the terms and conditions')
                                ->disabled(fn (callable $get): bool => (bool) $get('terms_locked'))
                                ->live()
                                ->afterStateUpdated(function (bool $state, TermAcceptanceServiceInterface $termAcceptanceService) use ($activity): void {
                                    $guardian = AuthUser::guardian();
                                    /** @var \App\Models\Term $term */
                                    $term = $activity->term;

                                    if ($state) {
                                        $termAcceptanceService->accept(
                                            $term,
                                            $guardian,
                                            request()->ip(),
                                            request()->userAgent()
                                        );
                                    } else {
                                        $termAcceptanceService->revoke($term, $guardian);
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
                                    ->action(function (
                                        callable $get,
                                        callable $set,
                                        TermAcceptanceServiceInterface $termAcceptanceService,
                                        GatepassServiceInterface $gatepassService,
                                    ) use ($activity): void {
                                        if ($activity->term !== null && ! $get('../../agree_to_terms')) {
                                            AppNotification::termsNotAgreed()->send();

                                            return;
                                        }

                                        $childId = $get('child_id');
                                        $guardianId = $get('guardian_id');

                                        if (empty($guardianId)) {
                                            return;
                                        }

                                        $requestingGuardian = AuthUser::guardian();
                                        /** @var Child|null $child */
                                        $child = Child::query()->find($childId);
                                        /** @var Guardian|null $checkinGuardian */
                                        $checkinGuardian = Guardian::query()->find($guardianId);

                                        if ($child === null || $checkinGuardian === null) {
                                            return;
                                        }

                                        $termAcceptance = null;
                                        if ($activity->term !== null) {
                                            $termAcceptance = $termAcceptanceService->getAcceptance(
                                                $activity->term,
                                                $requestingGuardian
                                            );
                                        }

                                        $gatepass = $gatepassService->create(
                                            $activity,
                                            $child,
                                            $checkinGuardian,
                                            $termAcceptance
                                        );

                                        $set('gatepass_code', $gatepass->code);
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
                ])),
            ])
            ->statePath('data');
    }
}
