# Naming Conventions

## PHP Classes
- Use `final class` for concrete classes

## Class Naming Patterns

| Type | Pattern | Example | Location |
|------|---------|---------|----------|
| Action (Business Logic) | `{Verb}{Noun}Action` | `CreateChildAction` | `app/Actions/` |
| Action (Filament UI) | `{Verb}{Noun}Action` | `CreateChildAction` | `app/Filament/Actions/` |
| Service | `{Domain}Service` | `ChildService` | `app/Services/` |
| Service Interface | `{Domain}ServiceInterface` | `ChildServiceInterface` | `app/Services/Contracts/` |
| Panel Provider | `{Panel}PanelProvider` | `KeeperPanelProvider` | `app/Providers/` |
| Scope | `{Entity}Scope` | `OrganizationScope` | `app/Models/Scopes/` |

## Filament Naming Patterns

| Type | Pattern | Example | Location |
|------|---------|---------|----------|
| Resource | `{Model}Resource` | `ChildResource` | `app/Filament/Panels/{Panel}/Resources/` |
| Resource Folder | Plural model | `Children/`, `Guardians/` | - |
| Form | `{Model}Form` | `ChildForm` | `Resources/{Model}/Schemas/` |
| Infolist | `{Model}Infolist` | `ChildInfolist` | `Resources/{Model}/Schemas/` |
| Table | `{PluralModel}Table` | `ChildrenTable` | `Resources/{Model}/Tables/` |
| Component | `App{ComponentType}` | `AppTextInput`, `AppSelect` | `app/Filament/Components/` |
| Notification | `AppNotification` | - | `app/Filament/Notifications/` |

## Method Naming

| Type | Pattern | Example |
|------|---------|---------|
| Static factories | `make()` | `AttendActivityAction::make()` |
| Field builders | Noun | `AppTextInput::firstName()` |
| Notifications | Past participle | `AppNotification::registeredToActivity()` |
| Qualified relationships | Qualifier + noun | `checkinAttendance()`, `ownedOrganizations()` |
