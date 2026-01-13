# Filament Infolist Components

## Purpose
Standardize infolist entry construction to:
- Separate entry configuration from layout concerns
- Enable reuse of common entry patterns across panels
- Keep schema arrays clean and focused on structure

---

## Architecture

### Component Categories

| Category | Description | Location |
|----------|-------------|----------|
| **Shared Components** | Commonly used across multiple infolists/panels | `app/Filament/Components/Infolists/App{Type}.php` |
| **Infolist-specific Components** | Used only within a single infolist | Private static methods within the Infolist class |

### What Goes Where

| Concern | Location | Examples |
|---------|----------|----------|
| **Entry attributes** | App Component or private method | `icon()`, `copyable()`, `placeholder()`, `date()`, `markdown()`, `formatStateUsing()`, `size()`, `weight()` |
| **Layout concerns** | Schema array (chained) | `columnSpanFull()`, `columnSpan()`, `hidden()` |

---

## Shared App Components

### Location
`app/Filament/Components/Infolists/`

### Naming Convention
- Class: `App{EntryType}` (e.g., `AppTextEntry`, `AppIconEntry`)
- Method: Noun describing the field (e.g., `firstName()`, `email()`, `avatar()`)

### Structure
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

### Available Components

| Class | Methods |
|-------|---------|
| `AppTextEntry` | `firstName()`, `middleName()`, `lastName()`, `fullName()`, `nickname()`, `email()`, `phone()`, `age()`, `birthday()`, `notes()`, `title()`, `content()`, `createdAt()`, `updatedAt()` |
| `AppIconEntry` | `gender()` |
| `AppSpatieMediaLibraryImageEntry` | `avatar()`, `thumbnail()` |

---

## Infolist-specific Components

### When to Use
- Entry is used only within a single infolist
- Entry has context-specific logic (record-based formatting, pivot relationships)
- Entry is part of a structural element (RepeatableEntry with specific schema)

### Structure
```php
final class GuardianInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        AppSpatieMediaLibraryImageEntry::avatar(),
                        Group::make([
                            AppTextEntry::fullName(),
                            AppTextEntry::age(),
                            AppTextEntry::birthday(),
                            AppIconEntry::gender(),
                            AppTextEntry::email(),
                            AppTextEntry::phone(),
                        ]),
                    ]),
                Section::make('Children')
                    ->schema([
                        self::childrenRepeatable(),
                    ]),
            ]);
    }

    private static function childrenRepeatable(): RepeatableEntry
    {
        return RepeatableEntry::make('children')
            ->hiddenLabel()
            ->schema([
                // Complex nested schema...
            ]);
    }
}
```

---

## Schema Array Rules

### Allowed in Schema Array
Only layout-related chaining:
- `columnSpanFull()`
- `columnSpan(int)`
- `hidden(Closure)`
- `visible(Closure)`

### NOT Allowed in Schema Array
Entry configuration must be in App Component or private method:
- `icon()`
- `copyable()`
- `placeholder()`
- `date()`, `dateTime()`
- `markdown()`, `prose()`
- `formatStateUsing()`
- `size()`, `weight()`
- `badge()`
- `collection()`, `circular()`, `defaultImageUrl()`
- Any other entry-specific configuration

---

## Examples

### Correct Usage
```php
return $schema->components([
    Section::make()
        ->schema([
            AppSpatieMediaLibraryImageEntry::avatar()
                ->columnSpanFull(),
            AppTextEntry::fullName(),
            AppTextEntry::nickname(),
            AppTextEntry::age(),
            AppTextEntry::birthday(),
            AppIconEntry::gender(),
        ]),
    Section::make('Notes')
        ->schema([
            AppTextEntry::notes()
                ->columnSpanFull(),
        ]),
]);
```

### Incorrect Usage
```php
// DON'T DO THIS
return $schema->components([
    SpatieMediaLibraryImageEntry::make('avatar')
        ->collection('avatar')
        ->circular()
        ->size(120)
        ->defaultImageUrl(fn ($record): string => Avatar::generateUrl($record->full_name))
        ->columnSpanFull(),
    TextEntry::make('full_name')
        ->hiddenLabel()
        ->size(TextSize::Large)
        ->weight(FontWeight::Bold),
]);
```

---

## Decision Guide

| Scenario | Solution |
|----------|----------|
| Entry used in 2+ infolists across panels | Create/update App Component |
| Entry used in 2+ infolists within same panel | Create/update App Component |
| Entry used only in one infolist | Private static method |
| Entry with pivot relationship formatting | Private static method |
| RepeatableEntry with specific schema | Private static method |
| Layout component (Section, Group, Grid) | Inline in schema array |
