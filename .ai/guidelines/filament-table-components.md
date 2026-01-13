# Filament Table Components

## Purpose
Standardize table column construction to:
- Separate column configuration from layout concerns
- Enable reuse of common column patterns across panels
- Keep columns arrays clean and focused on structure

---

## Architecture

### Component Categories

| Category | Description | Location |
|----------|-------------|----------|
| **Shared Components** | Commonly used across multiple tables/panels | `app/Filament/Components/Tables/App{Type}.php` |
| **Table-specific Components** | Used only within a single table | Private static methods within the Table class |

### What Goes Where

| Concern | Location | Examples |
|---------|----------|----------|
| **Column attributes** | App Component or private method | `searchable()`, `sortable()`, `date()`, `badge()`, `copyable()`, `description()`, `icon()` |
| **Layout concerns** | Columns array (chained) | `toggleable()`, `grow()`, `alignment()`, `width()` |

---

## Shared App Components

### Location
`app/Filament/Components/Tables/`

### Naming Convention
- Class: `App{ColumnType}` (e.g., `AppTextColumn`, `AppIconColumn`)
- Method: Noun describing the field (e.g., `firstName()`, `birthDate()`, `avatar()`)

### Structure
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

### Available Components

| Class | Methods |
|-------|---------|
| `AppTextColumn` | `firstName()`, `middleName()`, `lastName()`, `fullName()`, `nickname()`, `email()`, `phone()`, `birthDate()`, `title()`, `location()`, `code()`, `name()`, `type()`, `createdAt()`, `updatedAt()` |
| `AppIconColumn` | `gender()` |
| `AppSpatieMediaLibraryImageColumn` | `avatar()`, `thumbnail()` |
| `AppSpatieTagsColumn` | `tags()` |

---

## Table-specific Components

### When to Use
- Column is used only within a single table
- Column has context-specific logic (custom `searchable(query: ...)`, record-based formatting)
- Column uses complex Stack/Split layouts with nested columns
- Column requires custom `sortable(query: ...)` closures

### Structure
```php
final class GatepassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                AppTextColumn::code(),
                self::activityTitleColumn(),
                self::guardianFullNameColumn(),
                self::childFullNameColumn(),
            ]);
    }

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
                    ->orderBy('guardians.last_name', $direction)
                    ->select('gatepasses.*');
            });
    }
}
```

---

## Columns Array Rules

### Allowed in Columns Array
Only layout-related chaining:
- `toggleable()`
- `grow()`
- `alignment()`
- `width()`
- `hidden(Closure)`
- `visible(Closure)`

### NOT Allowed in Columns Array
Column configuration must be in App Component or private method:
- `searchable()`
- `sortable()`
- `date()`, `dateTime()`
- `badge()`
- `copyable()`
- `description()`
- `icon()`, `iconColor()`
- `formatStateUsing()`
- `size()`, `weight()`
- `collection()`, `circular()`, `defaultImageUrl()`
- `boolean()`
- Any other column-specific configuration

---

## Examples

### Correct Usage
```php
return $table->columns([
    AppSpatieMediaLibraryImageColumn::avatar(),
    AppTextColumn::firstName(),
    AppTextColumn::middleName(),
    AppTextColumn::lastName(),
    AppTextColumn::nickname(),
    AppTextColumn::birthDate(),
    AppIconColumn::gender(),
    AppSpatieTagsColumn::tags()
        ->toggleable(),
]);
```

### Incorrect Usage
```php
// DON'T DO THIS
return $table->columns([
    SpatieMediaLibraryImageColumn::make('avatar')
        ->collection('avatar')
        ->circular()
        ->defaultImageUrl(fn (Child $record): string => Avatar::generateUrl($record->full_name)),
    TextColumn::make('first_name')
        ->searchable(),
    TextColumn::make('birth_date')
        ->date('d M Y')
        ->description(fn (Child $record): string => "{$record->birth_date->age} yrs")
        ->sortable(),
]);
```

---

## Complex Column Layouts

For tables using Stack/Split layouts with complex nested columns, extract the entire layout structure to a private method:

```php
private static function avatarWithDetailsStack(): Stack
{
    return Stack::make([
        Split::make([
            AppSpatieMediaLibraryImageColumn::avatar()
                ->grow(false),
            Stack::make([
                self::nicknameColumn(),
                self::birthDateColumn(),
            ]),
        ]),
    ])->space(3);
}
```

---

## Decision Guide

| Scenario | Solution |
|----------|----------|
| Column used in 2+ tables across panels | Create/update App Component |
| Column used in 2+ tables within same panel | Create/update App Component |
| Column used only in one table | Private static method |
| Column with custom searchable query | Private static method |
| Column with custom sortable query | Private static method |
| Stack/Split layout with multiple columns | Private static method |
| Layout component (Stack, Split) | Can be inline or private method depending on complexity |
