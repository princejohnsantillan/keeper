# Naming Conventions

This document outlines the naming conventions used in this codebase. Follow these patterns for consistency.

---

## General Rules

| Rule | Convention |
|------|------------|
| **Strict Types** | Always declare `declare(strict_types=1);` at the top of every PHP file |
| **Class Declaration** | Use `final class` for concrete classes (not intended for extension) |
| **Namespace** | PSR-4 compliant |
| **Casing** | PascalCase for classes, enums, traits, interfaces |

---

## Classes by Type

### Models (`app/Models/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | Singular noun | `User`, `Child`, `Guardian`, `Activity` |
| **Casing** | PascalCase | `Organization`, `Gatepass` |
| **Declaration** | `final class` | `final class Child extends Model` |

### Enums (`app/Enums/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | Singular noun | `Gender`, `Relationship` |
| **Casing** | PascalCase | `InverseRelationship` |
| **Case Names** | TitleCase | `Male`, `Female`, `FamilyFriend` |
| **Backing Values** | snake_case strings or integers | `'family_friend'`, `1` |

```php
enum Gender: int implements HasColor, HasIcon, HasLabel
{
    case Male = 1;
    case Female = 0;
}

enum Relationship: string implements HasLabel
{
    case Father = 'father';
    case FamilyFriend = 'family_friend';
}
```

### Actions (`app/Filament/Actions/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | VerbFirst + Noun + `Action` suffix | `AttendActivityAction`, `CreateChildAction` |
| **Casing** | PascalCase | `CreateChildAction` |
| **Declaration** | `final class` | `final class CreateChildAction` |

### Middleware (`app/Http/Middleware/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | VerbFirst (action-oriented) | `RedirectGuardianDashboard`, `RequireOrganizationSubdomain` |
| **Casing** | PascalCase | `RequireOrganizationSubdomain` |
| **Declaration** | `final class` | `final class RedirectGuardianDashboard` |

### Service Providers (`app/Providers/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Service Providers** | `{Context}ServiceProvider` | `AppServiceProvider`, `FilamentServiceProvider` |
| **Panel Providers** | `{Panel}PanelProvider` | `KeeperPanelProvider`, `GuardianPanelProvider` |
| **Declaration** | `final class` | `final class HorizonServiceProvider` |

### Factories (`database/factories/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | `{Model}Factory` | `UserFactory`, `ChildFactory`, `GuardianFactory` |
| **Declaration** | `final class` | `final class ChildFactory extends Factory` |

### Scopes (`app/Models/Scopes/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | `{Entity}Scope` | `OrganizationScope` |
| **Declaration** | `final class` | `final class OrganizationScope implements Scope` |

---

## Filament-Specific Conventions

### Resources (`app/Filament/Panels/{Panel}/Resources/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | `{Model}Resource` | `ChildResource`, `GuardianResource`, `ActivityResource` |
| **Folder** | Plural model name | `Resources/Children/`, `Resources/Guardians/` |

### Resource Pages (`Resources/{Resource}/Pages/`)

| Action | Pattern | Example |
|--------|---------|---------|
| **List** | `List{PluralModel}` | `ListChildren`, `ListGuardians`, `ListActivities` |
| **View** | `View{SingularModel}` | `ViewChild`, `ViewAttendance` |
| **Create** | `Create{SingularModel}` | `CreateChild` |
| **Edit** | `Edit{SingularModel}` | `EditChild` |

### Schemas/Forms (`Resources/{Resource}/Schemas/`)

| Type | Pattern | Example |
|------|---------|---------|
| **Form** | `{Model}Form` | `ChildForm`, `GuardianForm`, `ActivityForm` |
| **Infolist** | `{Model}Infolist` | `ChildInfolist` |

### Tables (`Resources/{Resource}/Tables/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | `{PluralModel}Table` | `ChildrenTable`, `GuardiansTable`, `ActivitiesTable` |

### Custom Components (`app/Filament/Components/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | `App{ComponentType}` prefix | `AppTextInput`, `AppSelect`, `AppDatePicker` |

### Notifications (`app/Filament/Notifications/`)

| Aspect | Convention | Example |
|--------|------------|---------|
| **Name** | `App{Type}` prefix | `AppNotification` |

---

## Variable Naming

| Aspect | Convention | Example |
|--------|------------|---------|
| **Casing** | camelCase | `$organization`, `$childId`, `$guardianId` |
| **Style** | Descriptive nouns | `$startsAt`, `$existingAttendance` |
| **Collections** | Plural form | `$children`, `$guardians`, `$activities` |
| **Booleans** | Descriptive prefixes | `$existingAttendance`, `$alreadyCheckedOut` |

```php
// Method parameters
public function handle(Request $request, Closure $next): Response

// Local variables
$organization = Subdomain::organization();
$childId = $childData['child_id'];
$children = $guardian->children()->with('guardians')->get();
```

---

## Method Naming

| Aspect | Convention | Example |
|--------|------------|---------|
| **Casing** | camelCase | `getLabel()`, `canAccessPanel()` |
| **Actions** | VerbFirst | `generate()`, `inverse()`, `configure()` |
| **Getters** | `get{Property}` or noun | `getLabel()`, `getNickname()` |
| **Boolean checks** | `can{Action}`, `is{State}`, `has{Thing}` | `canAccessPanel()` |

### Eloquent Relationship Methods

| Relationship Type | Pattern | Example |
|-------------------|---------|---------|
| **belongsTo / hasOne** | Singular noun | `user()`, `guardian()`, `organization()` |
| **hasMany / belongsToMany** | Plural noun | `children()`, `guardians()`, `activities()` |
| **Qualified** | Qualifier + noun | `checkinAttendance()`, `ownedOrganizations()` |

```php
// Singular (belongsTo/hasOne)
public function guardian(): BelongsTo
public function organization(): BelongsTo

// Plural (hasMany/belongsToMany)
public function children(): BelongsToMany
public function activities(): HasMany

// Qualified relationships
public function checkinAttendance(): BelongsTo
public function ownedOrganizations(): HasMany
```

### Static Factory Methods

| Type | Pattern | Example |
|------|---------|---------|
| **General** | `make()` | `AttendActivityAction::make()` |
| **Configuration** | `configure()` | `ChildForm::configure($schema)` |
| **Field builders** | Field name as noun | `AppTextInput::firstName()`, `AppDatePicker::birthDate()` |
| **Notifications** | Past participle phrase | `AppNotification::registeredToActivity()` |

---

## Folder Structure Summary

```
app/
├── Console/Commands/          # {Verb}{Noun}Command or {Noun}Command
├── Enums/                     # Singular nouns (Gender, Relationship)
├── Filament/
│   ├── Actions/               # {Verb}{Noun}Action
│   ├── Components/
│   │   └── Forms/             # App{ComponentType}
│   ├── Notifications/         # App{Type}
│   └── Panels/
│       └── {Panel}/
│           └── Resources/
│               └── {PluralModel}/
│                   ├── {Model}Resource.php
│                   ├── Pages/     # {Action}{Model}
│                   ├── Schemas/   # {Model}Form, {Model}Infolist
│                   └── Tables/    # {PluralModel}Table
├── Http/
│   ├── Controllers/           # {Resource}Controller
│   └── Middleware/            # VerbFirst action names
├── Models/
│   └── Scopes/                # {Entity}Scope
├── Providers/                 # {Context}ServiceProvider, {Panel}PanelProvider
└── {UtilityClasses}.php       # Descriptive nouns (Avatar, Subdomain)

database/
├── factories/                 # {Model}Factory
└── seeders/                   # {Context}Seeder
```

---

## Quick Reference

| What You're Creating | Naming Pattern | Example |
|----------------------|----------------|---------|
| Model | Singular noun | `Child` |
| Enum | Singular noun | `Gender` |
| Enum Case | TitleCase | `FamilyFriend` |
| Action | `{Verb}{Noun}Action` | `CreateChildAction` |
| Middleware | VerbFirst | `RequireOrganizationSubdomain` |
| Resource | `{Model}Resource` | `ChildResource` |
| Resource List Page | `List{PluralModel}` | `ListChildren` |
| Resource View Page | `View{SingularModel}` | `ViewChild` |
| Form Schema | `{Model}Form` | `ChildForm` |
| Table | `{PluralModel}Table` | `ChildrenTable` |
| Factory | `{Model}Factory` | `ChildFactory` |
| Scope | `{Entity}Scope` | `OrganizationScope` |
| Service Provider | `{Context}ServiceProvider` | `AppServiceProvider` |
| Panel Provider | `{Panel}PanelProvider` | `KeeperPanelProvider` |
| Component | `App{ComponentType}` | `AppTextInput` |
| Variable | camelCase noun | `$childId`, `$guardians` |
| Method | camelCase, verb-first for actions | `generate()`, `canAccessPanel()` |
| Boolean method | `can`/`is`/`has` prefix | `canAccessPanel()` |
| Getter method | `get{Property}` | `getLabel()` |
| Relationship (singular) | Noun | `guardian()` |
| Relationship (plural) | Plural noun | `guardians()` |
