<?php

declare(strict_types=1);

namespace App\Filament\Panels\Keeper\Pages;

use App\Actions\WalkInRegistrationAction;
use App\Enums\Relationship as RelationshipEnum;
use App\Filament\Components\Forms\AppDatePicker;
use App\Filament\Components\Forms\AppSelect;
use App\Filament\Components\Forms\AppTextarea;
use App\Filament\Components\Forms\AppTextInput;
use App\Filament\Components\Forms\AppToggleButtons;
use App\Filament\Notifications\AppNotification;
use App\Models\Activity;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Http\Middleware\Authenticate;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * @property-read Schema $form
 */
final class WalkInRegistration extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static string $layout = 'filament-panels::components.layout.simple';

    protected static string|array $withoutRouteMiddleware = [Authenticate::class];

    protected string $view = 'filament-panels::pages.simple';

    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    public ?string $activityId = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function boot(): void
    {
        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Stone,
            'info' => Color::Sky,
            'primary' => Color::Lime,
            'success' => Color::Green,
            'warning' => Color::Orange,
        ]);
    }

    public function mount(): void
    {
        abort_unless(request()->hasValidSignature(), 403);

        $this->activityId = request()->query('activity');
        abort_unless($this->getActivity() !== null, 404);

        $this->form->fill();
    }

    public function getHeading(): string
    {
        return 'Walk-in Registration';
    }

    public function getSubheading(): ?string
    {
        return $this->getActivity()?->title;
    }

    public function hasLogo(): bool
    {
        return true;
    }

    public function getActivity(): ?Activity
    {
        if ($this->activityId === null) {
            return null;
        }

        return Activity::query()->find($this->activityId);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('register')
            ->footer([
                Actions::make([
                    Action::make('register')
                        ->label('Register Walk-in')
                        ->icon(Heroicon::UserPlus)
                        ->size('lg')
                        ->submit('register'),
                ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->termsSection(),
                $this->guardianSection(),
                $this->childrenSection(),
            ])
            ->statePath('data');
    }

    private function isPrivateActivity(): bool
    {
        return $this->getActivity()?->is_private === true;
    }

    public function register(): void
    {
        $data = $this->form->getState();
        $activity = $this->getActivity();

        $guardianData = [
            'first_name' => $data['guardian_first_name'],
            'middle_name' => $data['guardian_middle_name'] ?? null,
            'last_name' => $data['guardian_last_name'],
            'birth_date' => $data['guardian_birth_date'] ?? null,
            'gender' => $data['guardian_gender'],
            'email' => $data['guardian_email'],
            'phone' => $data['guardian_phone'] ?? null,
        ];

        $childrenData = collect($data['children'])->map(fn (array $child): array => [
            'data' => [
                'first_name' => $child['first_name'],
                'middle_name' => $child['middle_name'] ?? null,
                'last_name' => $child['last_name'],
                'nickname' => $child['nickname'] ?? null,
                'birth_date' => $child['birth_date'],
                'gender' => $child['gender'],
                'notes' => $child['notes'] ?? null,
            ],
            'relationship' => $child['relationship'] instanceof RelationshipEnum
                ? $child['relationship']
                : RelationshipEnum::from($child['relationship']),
            'invitation_code' => $child['invitation_code'] ?? null,
        ])->all();

        $agreedToTerms = (bool) ($data['agree_to_terms'] ?? false);

        app(WalkInRegistrationAction::class)(
            $guardianData,
            $childrenData,
            $activity,
            $agreedToTerms,
            request()->ip(),
            request()->userAgent(),
        );

        AppNotification::walkInRegistered()->send();

        $this->form->fill();
    }

    private function termsSection(): Section
    {
        $activity = $this->getActivity();

        return Section::make('Activity Terms')
            ->icon(Heroicon::DocumentText)
            ->visible($activity?->term !== null)
            ->schema([
                Placeholder::make('terms_content')
                    ->label('')
                    ->content(fn (): HtmlString => new HtmlString(
                        Str::markdown($activity?->term?->content ?? ''),
                    )),
                Checkbox::make('agree_to_terms')
                    ->label('I have read and agree to the terms and conditions for this activity.')
                    ->accepted(),
            ]);
    }

    private function guardianSection(): Section
    {
        return Section::make('Guardian Details')
            ->icon(Heroicon::UserCircle)
            ->columns(3)
            ->schema([
                AppTextInput::firstName('guardian_first_name', 'First name')
                    ->autofocus(),
                AppTextInput::middleName('guardian_middle_name', 'Middle name'),
                AppTextInput::lastName('guardian_last_name', 'Last name'),
                AppToggleButtons::gender('guardian_gender', 'Gender'),
                $this->guardianBirthDate(),
                AppTextInput::phone('guardian_phone', 'Phone'),
                AppTextInput::email('guardian_email', 'Email')
                    ->columnSpanFull(),
            ]);
    }

    private function guardianBirthDate(): DatePicker
    {
        return DatePicker::make('guardian_birth_date')
            ->label('Birth date')
            ->maxDate(now()->subYears(18))
            ->displayFormat('d M Y')
            ->required();
    }

    private function childrenSection(): Section
    {
        $isPrivate = $this->isPrivateActivity();

        return Section::make('Children')
            ->icon(Heroicon::UserGroup)
            ->schema([
                Repeater::make('children')
                    ->schema(array_filter([
                        $isPrivate ? TextInput::make('invitation_code')
                            ->label('Invitation Code')
                            ->required()
                            ->helperText('Enter the invitation code for this child.')
                            ->columnSpanFull() : null,
                        AppTextInput::firstName(),
                        AppTextInput::middleName(),
                        AppTextInput::lastName(),
                        AppTextInput::nickname(),
                        AppToggleButtons::gender(),
                        AppDatePicker::birthDate(),
                        AppSelect::relationship(),
                        AppTextarea::notes()
                            ->columnSpanFull(),
                    ]))
                    ->columns(3)
                    ->defaultItems(1)
                    ->minItems(1)
                    ->required()
                    ->addActionLabel('Add another child')
                    ->itemLabel(fn (array $state): ?string => $state['first_name'] ?? 'New child')
                    ->collapsible()
                    ->cloneable(),
            ]);
    }
}
