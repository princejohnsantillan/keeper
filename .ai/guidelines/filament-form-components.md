# Filament Form Components

## Purpose
Standardize form component construction to:
- Separate component configuration from layout concerns
- Enable reuse of common field patterns across panels
- Keep schema arrays clean and focused on structure

---

## Architecture

### Component Categories

| Category | Description | Location |
|----------|-------------|----------|
| **Shared Components** | Commonly used across multiple forms/panels | `app/Filament/Components/Forms/App{Type}.php` |
| **Form-specific Components** | Used only within a single form | Private static methods within the Form class |

### What Goes Where

| Concern | Location | Examples |
|---------|----------|----------|
| **Component attributes** | App Component or private method | `required()`, `email()`, `options()`, `collection()`, `circleCropper()`, `relationship()` |
| **Layout concerns** | Schema array (chained) | `columnSpanFull()`, `columnSpan()`, `autofocus()`, `columns()` |

---

## Shared App Components

### Location
`app/Filament/Components/Forms/`

### Naming Convention
- Class: `App{ComponentType}` (e.g., `AppTextInput`, `AppSelect`)
- Method: Noun describing the field (e.g., `firstName()`, `birthDate()`, `avatar()`)

### Structure
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

### Available Components

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

---

## Form-specific Components

### When to Use
- Component is used only within a single form
- Component has context-specific logic (relationships with query modifications, dynamic options)
- Component is a structural element (Repeater with specific schema)

### Structure
```php
final class GatepassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::code(),
                self::activitySelect()
                    ->columnSpanFull(),
                self::guardianSelect(),
                self::childSelect(),
            ])->columns(2);
    }

    private static function code(): TextInput
    {
        return TextInput::make('code')
            ->default(ReadableCode::generate())
            ->disabled()
            ->dehydrated()
            ->copyable()
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

---

## Schema Array Rules

### Allowed in Schema Array
Only layout-related chaining:
- `columnSpanFull()`
- `columnSpan(int)`
- `columns(int)`
- `autofocus()`
- `hidden(Closure)` (when conditionally showing/hiding based on form state)
- `disabled(Closure)` (when conditionally enabling/disabling based on form state)
- `visible(Closure)`

### NOT Allowed in Schema Array
Component configuration must be in App Component or private method:
- `required()`
- `email()`, `tel()`, `url()`
- `options()`, `relationship()`
- `rules()`, `maxLength()`, `minLength()`
- `collection()`, `image()`, `avatar()`, `imageEditor()`, `circleCropper()`
- `default()`, `dehydrated()`, `copyable()`
- `displayFormat()`, `maxDate()`, `minDate()`
- `native(false)`, `inline()`
- Any other component-specific configuration

---

## Examples

### Correct Usage
```php
return $schema->components([
    AppSpatieMediaLibraryFileUpload::avatar()
        ->columnSpanFull(),
    AppTextInput::firstName()
        ->autofocus(),
    AppTextInput::middleName(),
    AppTextInput::lastName(),
    AppDatePicker::birthDate(),
    AppToggleButtons::gender(),
    AppSpatieTagsInput::tags()
        ->columnSpanFull(),
    AppTextarea::notes()
        ->columnSpanFull(),
]);
```

### Incorrect Usage
```php
// DON'T DO THIS
return $schema->components([
    SpatieMediaLibraryFileUpload::make('avatar')
        ->collection('avatar')
        ->image()
        ->avatar()
        ->imageEditor()
        ->circleCropper()
        ->required()
        ->columnSpanFull(),
    TextInput::make('first_name')
        ->required(),
]);
```

---

## Action Form Schemas

Actions with form schemas follow the same rules. Extract component configuration to private methods within the Table/Page class.

### Example (Table with Action)
```php
final class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordActions([
                Action::make('walk_in')
                    ->schema([
                        Fieldset::make('Guardian Details')
                            ->schema([
                                AppTextInput::firstName('guardian_first_name', 'First Name')
                                    ->columnSpan(2),
                                AppTextInput::middleName('guardian_middle_name', 'Middle Name')
                                    ->columnSpan(2),
                                // ...
                            ])
                            ->columns(6)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
```

---

## Decision Guide

| Scenario | Solution |
|----------|----------|
| Field used in 2+ forms across panels | Create/update App Component |
| Field used in 2+ forms within same panel | Create/update App Component |
| Field used only in one form | Private static method |
| Select with dynamic relationship query | Private static method |
| Repeater with specific schema | Private static method |
| Layout component (Fieldset, Grid, Section) | Inline in schema array |
