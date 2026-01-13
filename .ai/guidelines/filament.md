# Filament Guidelines

## Core Principle
Separate configuration from layout. All component/column/entry attributes go in reusable App Components or private methods. Only layout concerns are chained in schema/columns arrays.

---

## Directory Structure

| Type | Location |
|------|----------|
| Filament Actions | `app/Filament/Actions/` |
| Form Components | `app/Filament/Components/Forms/` |
| Infolist Components | `app/Filament/Components/Infolists/` |
| Table Components | `app/Filament/Components/Tables/` |
| Business Actions | `app/Actions/` |
| Services | `app/Services/` |
| Service Interfaces | `app/Services/Contracts/` |
| Resources | `app/Filament/Panels/{Panel}/Resources/` |
| Notifications | `app/Filament/Notifications/` |

---

## Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Resource | `{Model}Resource` | `ChildResource` |
| Form Schema | `{Model}Form` | `ChildForm` |
| Infolist Schema | `{Model}Infolist` | `ChildInfolist` |
| Table Schema | `{PluralModel}Table` | `ChildrenTable` |
| Filament Action | `{Verb}{Noun}Action` | `CreateChildAction` |
| Business Action | `{Verb}{Noun}Action` | `CreateChildAction` |
| Form Component | `App{Type}` | `AppTextInput` |
| Infolist Component | `App{Type}` | `AppTextEntry` |
| Table Component | `App{Type}` | `AppTextColumn` |
| Service | `{Domain}Service` | `ChildService` |
| Service Interface | `{Domain}ServiceInterface` | `ChildServiceInterface` |

### Method Naming
- Static factory: `make()`
- Field/entry/column builder: Noun (`firstName()`, `birthDate()`, `avatar()`)
- Private complex component: `{field}Column()`, `{field}Entry()`
- Notification factory: Past participle (`registeredToActivity()`)

### Variable Naming
Never use `$action` for Filament action variables (reserved in callbacks). Use full class name in camelCase:
```php
$createChildAction = CreateChildAction::make(); // Correct
$action = CreateChildAction::make();            // WRONG
```

---

## Schema/Columns Array Rules

### Allowed (Layout Only)
```php
->columnSpanFull()
->columnSpan(int)
->columns(int)
->autofocus()
->toggleable()
->grow()
->alignment()
->width()
->hidden(Closure)
->visible(Closure)
->disabled(Closure) // conditional only
```

### NOT Allowed (Must Be in App Component or Private Method)
```php
// Form
->required(), ->email(), ->tel(), ->url(), ->options(), ->relationship()
->rules(), ->maxLength(), ->minLength(), ->default(), ->dehydrated()
->collection(), ->image(), ->avatar(), ->imageEditor(), ->circleCropper()
->displayFormat(), ->maxDate(), ->minDate(), ->native(false), ->inline()

// Infolist
->icon(), ->copyable(), ->placeholder(), ->date(), ->dateTime()
->markdown(), ->prose(), ->formatStateUsing(), ->size(), ->weight()
->badge(), ->circular(), ->defaultImageUrl()

// Table
->searchable(), ->sortable(), ->date(), ->dateTime(), ->badge()
->copyable(), ->description(), ->icon(), ->iconColor(), ->boolean()
->formatStateUsing(), ->size(), ->weight()
```

---

## App Component Structure

### Forms (`app/Filament/Components/Forms/`)
```php
declare(strict_types=1);

namespace App\Filament\Components\Forms;

use Filament\Forms\Components\TextInput;

final class AppTextInput
{
    public static function firstName(string $field = 'first_name', string $label = 'First name'): TextInput
    {
        return TextInput::make($field)->label($label)
            ->required()
            ->rules(['max:80']);
    }
}
```

### Infolists (`app/Filament/Components/Infolists/`)
```php
declare(strict_types=1);

namespace App\Filament\Components\Infolists;

use Filament\Infolists\Components\TextEntry;

final class AppTextEntry
{
    public static function email(string $field = 'email', string $label = 'Email'): TextEntry
    {
        return TextEntry::make($field)->label($label)
            ->icon('heroicon-o-envelope')
            ->copyable()
            ->placeholder('—');
    }
}
```

### Tables (`app/Filament/Components/Tables/`)
```php
declare(strict_types=1);

namespace App\Filament\Components\Tables;

use Filament\Tables\Columns\TextColumn;

final class AppTextColumn
{
    public static function firstName(string $field = 'first_name', string $label = 'First name'): TextColumn
    {
        return TextColumn::make($field)->label($label)
            ->searchable()
            ->sortable();
    }
}
```

---

## Usage Examples

### Correct Form Schema
```php
return $schema->components([
    AppSpatieMediaLibraryFileUpload::avatar()
        ->columnSpanFull(),
    AppTextInput::firstName()
        ->autofocus(),
    AppTextInput::lastName(),
    AppDatePicker::birthDate(),
    AppToggleButtons::gender(),
    AppTextarea::notes()
        ->columnSpanFull(),
]);
```

### Correct Table Columns
```php
return $table->columns([
    AppSpatieMediaLibraryImageColumn::avatar(),
    AppTextColumn::firstName(),
    AppTextColumn::lastName(),
    AppTextColumn::birthDate(),
    AppIconColumn::gender(),
    AppSpatieTagsColumn::tags()
        ->toggleable(),
]);
```

### Correct Infolist Schema
```php
return $schema->components([
    Section::make()
        ->schema([
            AppSpatieMediaLibraryImageEntry::avatar()
                ->columnSpanFull(),
            AppTextEntry::fullName(),
            AppTextEntry::birthday(),
            AppIconEntry::gender(),
        ]),
]);
```

### WRONG (Configuration in Schema)
```php
// DON'T DO THIS
return $schema->components([
    TextInput::make('first_name')
        ->required()          // Should be in App Component
        ->rules(['max:80']),  // Should be in App Component
]);
```

---

## Filament Actions

### Structure (`app/Filament/Actions/`)
```php
declare(strict_types=1);

namespace App\Filament\Actions;

use Filament\Actions\Action;

final class DoSomethingAction
{
    public static function make(?string $name = null, string $label = 'Do Something'): Action
    {
        return Action::make($name)->label($label)
            // configuration...
    }
}
```

### Business Logic Delegation
Filament actions delegate to business actions/services. No direct model manipulation.

```php
// Correct: Delegate to business action
->using(fn (array $data, CreateChildAction $createChild): Child => $createChild($data))

// WRONG: Business logic in Filament action
->using(function (array $data): Child {
    return Child::query()->create($data); // Should be in app/Actions/
});
```

---

## Business Actions (`app/Actions/`)

Single-responsibility invokable classes. No UI logic.

```php
declare(strict_types=1);

namespace App\Actions;

use App\Models\Child;

final class CreateChildAction
{
    public function __invoke(array $data): Child
    {
        return Child::query()->create($data);
    }
}
```

### With Dependencies
```php
final class CreateChildAction
{
    public function __construct(
        private NotificationServiceInterface $notificationService,
    ) {}

    public function __invoke(array $data): Child
    {
        $child = Child::query()->create($data);
        $this->notificationService->notifyGuardian($child);
        return $child;
    }
}
```

---

## Services (`app/Services/`)

Related domain operations grouped together. Must have interface.

### Interface (`app/Services/Contracts/`)
```php
interface ChildServiceInterface
{
    public function create(array $data): Child;
    public function attachGuardian(Child $child, Guardian $guardian, string $relationship): void;
}
```

### Implementation
```php
final class ChildService implements ChildServiceInterface
{
    public function create(array $data): Child
    {
        return Child::query()->create($data);
    }

    public function attachGuardian(Child $child, Guardian $guardian, string $relationship): void
    {
        Relationship::query()->create([
            'child_id' => $child->id,
            'guardian_id' => $guardian->id,
            'relationship' => $relationship,
        ]);
    }
}
```

### Binding (`app/Providers/ServiceServiceProvider.php`)
```php
public array $bindings = [
    ChildServiceInterface::class => ChildService::class,
];
```

---

## Private Methods for Complex/Context-Specific Components

Use when component is used only within a single form/table/infolist, has custom queries, or complex layouts.

### Form Example
```php
final class GatepassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            self::code(),
            self::activitySelect()
                ->columnSpanFull(),
        ])->columns(2);
    }

    private static function code(): TextInput
    {
        return TextInput::make('code')
            ->default(ReadableCode::generate())
            ->disabled()
            ->dehydrated()
            ->required();
    }

    private static function activitySelect(): Select
    {
        return Select::make('activity_id')
            ->relationship('activity', 'title')
            ->required();
    }
}
```

### Table Example (Custom Searchable/Sortable)
```php
private static function guardianFullNameColumn(): TextColumn
{
    return TextColumn::make('guardian.full_name')
        ->searchable(query: function (Builder $query, string $search): Builder {
            return $query->whereHas('guardian', function (Builder $query) use ($search): void {
                $query->where('first_name', 'ilike', "%{$search}%")
                    ->orWhere('last_name', 'ilike', "%{$search}%");
            });
        })
        ->sortable(query: function (Builder $query, string $direction): Builder {
            return $query
                ->join('guardians', 'gatepasses.guardian_id', '=', 'guardians.id')
                ->orderBy('guardians.first_name', $direction)
                ->select('gatepasses.*');
        });
}
```

---

## Decision Guide

| Scenario | Solution |
|----------|----------|
| Component used in 2+ forms/tables/infolists | App Component |
| Component used only in one place | Private static method |
| Custom searchable/sortable query | Private static method |
| Complex Stack/Split/Repeater layout | Private static method |
| Layout component (Section, Grid, Fieldset) | Inline in schema |
| Single focused operation | Business Action |
| Multiple related domain operations | Service |
| Simple CRUD, no side effects | Business Action |
| Complex multi-step orchestration | Business Action (inject services) |

---

## Available App Components

### Forms
| Class | Methods |
|-------|---------|
| `AppTextInput` | `firstName()`, `middleName()`, `lastName()`, `nickname()`, `email()`, `phone()`, `title()`, `name()`, `location()`, `type()` |
| `AppSelect` | `relationship()`, `term()`, `gender()` |
| `AppDatePicker` | `birthDate()` |
| `AppDateTimePicker` | `startsAt()`, `endsAt()`, `publishedAt()` |
| `AppTextarea` | `notes()`, `description()` |
| `AppToggleButtons` | `gender()` |
| `AppSpatieMediaLibraryFileUpload` | `avatar()`, `thumbnail()` |
| `AppSpatieTagsInput` | `tags()` |
| `AppMarkdownEditor` | `content()` |

### Infolists
| Class | Methods |
|-------|---------|
| `AppTextEntry` | `firstName()`, `middleName()`, `lastName()`, `fullName()`, `nickname()`, `email()`, `phone()`, `age()`, `birthday()`, `notes()`, `title()`, `content()`, `createdAt()`, `updatedAt()` |
| `AppIconEntry` | `gender()` |
| `AppSpatieMediaLibraryImageEntry` | `avatar()`, `thumbnail()` |

### Tables
| Class | Methods |
|-------|---------|
| `AppTextColumn` | `firstName()`, `middleName()`, `lastName()`, `fullName()`, `nickname()`, `email()`, `phone()`, `birthDate()`, `title()`, `location()`, `code()`, `name()`, `type()`, `createdAt()`, `updatedAt()` |
| `AppIconColumn` | `gender()` |
| `AppSpatieMediaLibraryImageColumn` | `avatar()`, `thumbnail()` |
| `AppSpatieTagsColumn` | `tags()` |
