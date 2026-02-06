<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities\Pages;

use App\AuthUser;
use App\Filament\Actions\BackToActivitiesAction;
use App\Filament\Notifications\AppNotification;
use App\Filament\Panels\Guardian\Resources\Activities\ActivityResource;
use App\Filament\Panels\Guardian\Resources\Gatepasses\GatepassResource;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
use App\Models\Guardian;
use App\Services\Contracts\GatepassServiceInterface;
use App\Services\Contracts\TermAcceptanceServiceInterface;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * @property-read Schema $form
 */
final class RegisterActivity extends Page
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

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            BackToActivitiesAction::make(),
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

        $termAcceptanceService = app(TermAcceptanceServiceInterface::class);

        $hasAcceptance = false;

        if ($activity->term !== null) {
            $termAcceptance = $termAcceptanceService->getAcceptance($activity->term, $guardian);
            $hasAcceptance = $termAcceptance !== null;
        }

        $this->form->fill([
            'agree_to_terms' => $hasAcceptance,
            'child_id' => null,
            'guardian_id' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        /** @var Activity $activity */
        $activity = $this->getRecord();

        return $schema
            ->components([
                Form::make(array_filter([
                    $activity->description !== null ? self::descriptionPlaceholder($activity) : null,
                    $activity->term !== null ? self::termsFieldset($activity) : null,
                    self::registrationSection($activity),
                    self::existingGatepassesSection($activity),
                ])),
            ])
            ->statePath('data');
    }

    private static function descriptionPlaceholder(Activity $activity): TextEntry
    {
        return TextEntry::make('description')
            ->label('Description')
            ->state(new HtmlString(
                '<div class="fi-prose prose dark:prose-invert max-w-none">'.
                Str::markdown($activity->description ?? '').
                '</div>'
            ))
            ->html();
    }

    private static function termsFieldset(Activity $activity): Fieldset
    {
        return Fieldset::make('Terms and Conditions')
            ->columnSpanFull()
            ->columns(1)
            ->extraAttributes(['class' => 'bg-white dark:bg-gray-900 rounded-xl'])
            ->schema([
                self::termsContentPlaceholder($activity),
                self::agreeToTermsCheckbox($activity),
            ]);
    }

    private static function termsContentPlaceholder(Activity $activity): TextEntry
    {
        return TextEntry::make('terms_content')
            ->hiddenLabel()
            ->state(new HtmlString(
                '<div class="fi-prose prose dark:prose-invert max-w-none">'.
                Str::markdown($activity->term?->content ?: '').
                '</div>'
            ))
            ->html();
    }

    private static function agreeToTermsCheckbox(Activity $activity): Checkbox
    {
        return Checkbox::make('agree_to_terms')
            ->label('I have read and agree to the terms and conditions')
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
            });
    }

    private static function registrationSection(Activity $activity): Section
    {
        return Section::make('Register for Activity')
            ->key('registration-section')
            ->compact()
            ->columns(2)
            ->schema([
                self::childSelect(),
                self::guardianSelect(),
            ])
            ->footerActions([
                self::registerAction($activity),
            ]);
    }

    private static function childSelect(): Select
    {
        return Select::make('child_id')
            ->label('Child')
            ->options(function (): array {
                $guardian = AuthUser::guardian();

                return $guardian->children()->get()->pluck('full_name', 'id')->toArray();
            })
            ->searchable()
            ->live()
            ->afterStateUpdated(fn (callable $set) => $set('guardian_id', null))
            ->required();
    }

    private static function guardianSelect(): Select
    {
        return Select::make('guardian_id')
            ->label('Guardian for Check-in/out')
            ->options(function (callable $get): array {
                $childId = $get('child_id');

                if (empty($childId)) {
                    return [];
                }

                /** @var Child|null $child */
                $child = Child::query()->with('guardians')->find($childId);

                if ($child === null) {
                    return [];
                }

                return $child->guardians->pluck('full_name', 'id')->toArray();
            })
            ->searchable()
            ->required();
    }

    private static function registerAction(Activity $activity): Action
    {
        return Action::make('register')
            ->label('Register')
            ->color('primary')
            ->action(function (
                Get $schemaGet,
                Set $schemaSet,
                TermAcceptanceServiceInterface $termAcceptanceService,
                GatepassServiceInterface $gatepassService,
            ) use ($activity): void {
                $agreeToTerms = $schemaGet('agree_to_terms');
                $childId = $schemaGet('child_id');
                $guardianId = $schemaGet('guardian_id');

                if ($activity->term !== null && ! $agreeToTerms) {
                    AppNotification::termsNotAgreed()->send();

                    return;
                }

                if (empty($childId) || empty($guardianId)) {
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

                $existingGatepass = $gatepassService->findExisting($activity, $child, $checkinGuardian);

                if ($existingGatepass !== null) {
                    $gatepassUrl = GatepassResource::getUrl('view', ['record' => $existingGatepass]);
                    AppNotification::alreadyRegisteredForActivity($gatepassUrl)->send();

                    return;
                }

                $termAcceptance = null;
                if ($activity->term !== null) {
                    $termAcceptance = $termAcceptanceService->getAcceptance(
                        $activity->term,
                        $requestingGuardian
                    );
                }

                $gatepassService->create(
                    $activity,
                    $child,
                    $checkinGuardian,
                    $termAcceptance
                );

                $schemaSet('child_id', null);
                $schemaSet('guardian_id', null);

                AppNotification::registeredToActivity()->send();

                redirect(ActivityResource::getUrl('register', ['record' => $activity]));
            });
    }

    private static function existingGatepassesSection(Activity $activity): Section
    {
        $guardian = AuthUser::guardian();
        $childIds = $guardian->children()->pluck('children.id');

        $gatepasses = Gatepass::query()
            ->where('activity_id', $activity->id)
            ->whereIn('child_id', $childIds)
            ->with(['child', 'guardian'])
            ->get();

        if ($gatepasses->isEmpty()) {
            return Section::make('Registered Children')
                ->icon('entypo-lock')
                ->compact()
                ->schema([
                    TextEntry::make('no_registrations')
                        ->hiddenLabel()
                        ->state('No children registered yet.'),
                ]);
        }

        $content = $gatepasses->map(function (Gatepass $gatepass): string {
            return sprintf(
                '<div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700 last:border-0"><span class="font-medium">%s</span><span class="text-gray-500 dark:text-gray-400">with %s</span><code class="px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-sm font-mono">%s</code></div>',
                e($gatepass->child->full_name),
                e($gatepass->guardian->full_name),
                e($gatepass->code)
            );
        })->implode('');

        return Section::make('Registered Children')
            ->icon('entypo-lock')
            ->compact()
            ->schema([
                TextEntry::make('registrations')
                    ->hiddenLabel()
                    ->state(new HtmlString('<div class="divide-y divide-gray-200 dark:divide-gray-700">'.$content.'</div>'))
                    ->html(),
            ]);
    }
}
