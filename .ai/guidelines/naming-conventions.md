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
| Component | `App{Type}` | `AppTextInput`, `AppSelect` | `app/Filament/Components/` |
| Notification | `AppNotification` | - | `app/Filament/Notifications/` |

## Method Patterns

| Type | Convention | Example |
|------|------------|---------|
| Static factory | `make()` | `AttendActivityAction::make()` |
| Field builder | Noun | `AppTextInput::firstName()` |
| Notification factory | Past participle | `AppNotification::registeredToActivity()` |
| Qualified relationship | Qualifier + noun | `checkinAttendance()`, `ownedOrganizations()` |
