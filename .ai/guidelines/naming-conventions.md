# Naming Conventions

## PHP Classes
- Always use `final class` for concrete implementations

## Class Patterns

| Type | Pattern | Example | Location |
|------|---------|---------|----------|
| Action (Business) | `{Verb}{Noun}Action` | `CreateChildAction` | `app/Actions/` |
| Action (Filament) | `{Verb}{Noun}Action` | `CreateChildAction` | `app/Filament/Actions/` |
| Service | `{Domain}Service` | `ChildService` | `app/Services/` |
| Service Interface | `{Domain}ServiceInterface` | `ChildServiceInterface` | `app/Services/Contracts/` |
| Panel Provider | `{Panel}PanelProvider` | `KeeperPanelProvider` | `app/Providers/` |
| Scope | `{Entity}Scope` | `OrganizationScope` | `app/Models/Scopes/` |

## Filament Patterns

| Type | Pattern | Example | Location |
|------|---------|---------|----------|
| Resource | `{Model}Resource` | `ChildResource` | `app/Filament/Panels/{Panel}/Resources/` |
| Resource Folder | Plural | `Children/`, `Guardians/` | - |
| Form | `{Model}Form` | `ChildForm` | `Resources/{Model}/Schemas/` |
| Infolist | `{Model}Infolist` | `ChildInfolist` | `Resources/{Model}/Schemas/` |
| Table | `{PluralModel}Table` | `ChildrenTable` | `Resources/{Model}/Tables/` |
| Form Component | `App{Type}` | `AppTextInput`, `AppSelect` | `app/Filament/Components/Forms/` |
| Infolist Component | `App{Type}` | `AppTextEntry`, `AppIconEntry` | `app/Filament/Components/Infolists/` |
| Table Component | `App{Type}` | `AppTextColumn`, `AppIconColumn` | `app/Filament/Components/Tables/` |
| Notification | `AppNotification` | - | `app/Filament/Notifications/` |

## Method Patterns

| Type | Convention | Example |
|------|------------|---------|
| Static factory | `make()` | `AttendActivityAction::make()` |
| Form field builder | Noun | `AppTextInput::firstName()` |
| Infolist entry builder | Noun | `AppTextEntry::fullName()` |
| Table column builder | Noun | `AppTextColumn::birthDate()` |
| Notification factory | Past participle | `AppNotification::registeredToActivity()` |
| Qualified relationship | Qualifier + noun | `checkinAttendance()`, `ownedOrganizations()` |
| Complex column (private) | `{field}Column()` | `self::childrenColumn()` |
| Complex entry (private) | `{field}Entry()` | `self::descriptionEntry()` |

## Variable Naming

| Type | Convention | Example |
|------|------------|---------|
| Filament action variable | Full class name in camelCase | `$createChildAction = CreateChildAction::make()` |

**Note:** Never use `$action` as a variable name for Filament actions. It is a reserved parameter name in Filament callbacks. See [Filament Actions guidelines](./filament-actions.md) for details.
