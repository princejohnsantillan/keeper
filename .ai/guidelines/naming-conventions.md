# Naming Conventions

## PHP File Structure
- Always declare `declare(strict_types=1);` at top
- Use `final class` for concrete classes
- PSR-4 compliant namespaces
- PascalCase for classes, enums, traits, interfaces

## Class Naming Patterns

| Type | Pattern | Example | Location |
|------|---------|---------|----------|
| Model | Singular noun | `Child`, `Guardian`, `Activity` | `app/Models/` |
| Enum | Singular noun | `Gender`, `Relationship` | `app/Enums/` |
| Enum Case | TitleCase | `Male`, `FamilyFriend` | - |
| Enum Backing | snake_case or int | `'family_friend'`, `1` | - |
| Action | `{Verb}{Noun}Action` | `CreateChildAction` | `app/Filament/Actions/` |
| Middleware | VerbFirst | `RequireOrganizationSubdomain` | `app/Http/Middleware/` |
| Service Provider | `{Context}ServiceProvider` | `AppServiceProvider` | `app/Providers/` |
| Panel Provider | `{Panel}PanelProvider` | `KeeperPanelProvider` | `app/Providers/` |
| Factory | `{Model}Factory` | `ChildFactory` | `database/factories/` |
| Scope | `{Entity}Scope` | `OrganizationScope` | `app/Models/Scopes/` |

## Filament Naming Patterns

| Type | Pattern | Example | Location |
|------|---------|---------|----------|
| Resource | `{Model}Resource` | `ChildResource` | `app/Filament/Panels/{Panel}/Resources/` |
| Resource Folder | Plural model | `Children/`, `Guardians/` | - |
| List Page | `List{PluralModel}` | `ListChildren` | `Resources/{Model}/Pages/` |
| View Page | `View{Model}` | `ViewChild` | `Resources/{Model}/Pages/` |
| Create Page | `Create{Model}` | `CreateChild` | `Resources/{Model}/Pages/` |
| Edit Page | `Edit{Model}` | `EditChild` | `Resources/{Model}/Pages/` |
| Form | `{Model}Form` | `ChildForm` | `Resources/{Model}/Schemas/` |
| Infolist | `{Model}Infolist` | `ChildInfolist` | `Resources/{Model}/Schemas/` |
| Table | `{PluralModel}Table` | `ChildrenTable` | `Resources/{Model}/Tables/` |
| Component | `App{ComponentType}` | `AppTextInput`, `AppSelect` | `app/Filament/Components/` |
| Notification | `App{Type}` | `AppNotification` | `app/Filament/Notifications/` |

## Variable & Method Naming

| Type | Pattern | Example |
|------|---------|---------|
| Variables | camelCase | `$childId`, `$organization` |
| Collections | Plural | `$children`, `$guardians` |
| Booleans | Descriptive | `$existingAttendance`, `$alreadyCheckedOut` |
| Methods | camelCase, VerbFirst for actions | `generate()`, `configure()` |
| Getters | `get{Property}` or noun | `getLabel()`, `getNickname()` |
| Boolean checks | `can`/`is`/`has` prefix | `canAccessPanel()`, `isActive()` |
| belongsTo/hasOne | Singular noun | `guardian()`, `organization()` |
| hasMany/belongsToMany | Plural noun | `children()`, `activities()` |
| Qualified relationships | Qualifier + noun | `checkinAttendance()`, `ownedOrganizations()` |
| Static factories | `make()` | `AttendActivityAction::make()` |
| Field builders | Noun | `AppTextInput::firstName()` |
| Notifications | Past participle | `AppNotification::registeredToActivity()` |
