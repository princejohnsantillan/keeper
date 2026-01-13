<?php

declare(strict_types=1);

namespace App\Filament\Panels\Guardian\Resources\Activities\Pages;

use App\AuthUser;
use App\Filament\Actions\BackToActivitiesAction;
use App\Filament\Actions\RequestGatePassAction;
use App\Filament\Panels\Guardian\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\Child;
use App\Models\Gatepass;
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
                    $activity->term !== null ? self::termsFieldset($activity) : null,
                    self::childrenRepeater($activity),
                ])),
            ])
            ->statePath('data');
    }

    private static function termsFieldset(Activity $activity): Fieldset
    {
        return Fieldset::make('Terms and Conditions')
            ->columnSpanFull()
            ->columns(1)
            ->schema([
                self::termsContentPlaceholder($activity),
                self::termsLockedHidden(),
                self::agreeToTermsCheckbox($activity),
            ]);
    }

    private static function termsContentPlaceholder(Activity $activity): Placeholder
    {
        return Placeholder::make('terms_content')
            ->hiddenLabel()
            ->state(new HtmlString(
                '<div class="fi-prose prose dark:prose-invert max-w-none max-h-64 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">'.
                Str::markdown($activity->term?->content ?: '').
                '</div>'
            ))
            ->html();
    }

    private static function termsLockedHidden(): Hidden
    {
        return Hidden::make('terms_locked');
    }

    private static function agreeToTermsCheckbox(Activity $activity): Checkbox
    {
        return Checkbox::make('agree_to_terms')
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
            });
    }

    private static function childrenRepeater(Activity $activity): Repeater
    {
        return Repeater::make('children')
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
                self::childIdHidden(),
                self::childNameInput(),
                self::guardianSelect(),
                self::gatepassFlex($activity),
            ]);
    }

    private static function childIdHidden(): Hidden
    {
        return Hidden::make('child_id');
    }

    private static function childNameInput(): TextInput
    {
        return TextInput::make('child_name')
            ->label('Child')
            ->disabled()
            ->dehydrated(false);
    }

    private static function guardianSelect(): Select
    {
        return Select::make('guardian_id')
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
            ->required();
    }

    private static function gatepassFlex(Activity $activity): Flex
    {
        return Flex::make([
            self::gatepassCodeHidden(),
            RequestGatePassAction::make($activity),
            self::gatepassDisplayPlaceholder(),
        ]);
    }

    private static function gatepassCodeHidden(): Hidden
    {
        return Hidden::make('gatepass_code');
    }

    private static function gatepassDisplayPlaceholder(): Placeholder
    {
        return Placeholder::make('gatepass_display')
            ->label('Gate Pass')
            ->hiddenLabel()
            ->content(fn (callable $get): ?string => $get('gatepass_code'))
            ->hidden(fn (callable $get): bool => empty($get('gatepass_code')));
    }
}
