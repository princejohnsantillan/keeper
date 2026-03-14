# Second Opinion Request

## Question
# Review Request

## Question
Create a plan to add a way for keepers to edit information of a child or guardian only if they are the owner of those records, this is defined by the `ownership` table. When editing a guardian make it in a way that they can manage related children as well. When editing a child make it in a way that they can manage related guardians as well.

## Context

### Ownership System
The ownership system uses a polymorphic `ownership` table connecting owners (User or Organization) to models (Child, Guardian).

Key files:
- @app/Models/Ownership.php - Polymorphic pivot model with `owner()` and `model()` MorphTo relationships
- @app/Models/Concerns/HasOwnership.php - Trait providing `ownerships()` MorphMany and `scopeOwnedBy(Model $owner)` scope
- @database/migrations/2026_02_07_003800_create_ownership_table.php - Table: owner_type, owner_id, model_type, model_id (unique constraint)

### Models
- @app/Models/Child.php - Uses HasOwnership trait. Has `guardians()` BelongsToMany via `relationships` pivot
- @app/Models/Guardian.php - Uses HasOwnership trait. Has `children()` BelongsToMany via `relationships` pivot
- @app/Models/Relationship.php - Pivot model (extends Pivot) with guardian_id, child_id, relationship (enum), is_primary
- @app/Models/Keeper.php - Staff member: belongs to Organization and User. Has roles (Admin, Gatekeeper) and status (Active, Inactive, Pending)
- @app/Models/User.php - Auth user. Has `keeper()` HasOne, `guardian()` BelongsTo, `ownedOrganizations()` HasMany
- @app/AuthUser.php - Static helper: `user()`, `guardian()`, `guardianId()`, `userId()`

### Keeper Panel (Admin) - Current State
The Keeper panel currently has VIEW ONLY pages for children and guardians with NO edit actions:
- @app/Filament/Panels/Keeper/Resources/Children/ChildResource.php - Scoped query with `ownedBy($organization)`, eager loads guardians
- @app/Filament/Panels/Keeper/Resources/Children/Pages/ViewChild.php - View only, no header actions
- @app/Filament/Panels/Keeper/Resources/Children/Schemas/ChildForm.php - Form: avatar, names, nickname, birth_date, gender, tags, notes
- @app/Filament/Panels/Keeper/Resources/Guardians/GuardianResource.php - Scoped query with `ownedBy($organization)`, eager loads children
- @app/Filament/Panels/Keeper/Resources/Guardians/Pages/ViewGuardian.php - View only, no header actions
- @app/Filament/Panels/Keeper/Resources/Guardians/Schemas/GuardianForm.php - Form: avatar, names, birth_date, gender, email, phone, tags

### Guardian Panel - Has Edit Actions Already
The Guardian panel already has working edit/delete actions:
- @app/Filament/Panels/Guardian/Resources/Children/Pages/ViewChild.php - Has edit/delete header actions
- @app/Filament/Panels/Guardian/Resources/Guardians/Pages/ViewGuardian.php - Has edit/delete header actions
- @app/Filament/Panels/Guardian/Resources/Children/Schemas/ChildForm.php - Includes "Relationships with Guardians" repeater fieldset
- @app/Filament/Panels/Guardian/Resources/Guardians/Schemas/GuardianForm.php - Includes "Relationships with Children" repeater fieldset

### Existing Filament Actions (app/Filament/Actions/)
- @app/Filament/Actions/EditChildAction.php - SlideOver modal. Mutates data to include guardians. Uses UpdateChildAction + SyncChildGuardiansAction
- @app/Filament/Actions/EditGuardianAction.php - SlideOver modal. Mutates data to include children. Uses UpdateGuardianAction
- @app/Filament/Actions/CreateChildAction.php - Creates child with guardians. Uses CreateChildBusinessAction + SyncChildGuardiansAction
- @app/Filament/Actions/DeleteChildAction.php - Uses DetachChildFromGuardianAction. Visible only in Guardian panel
- @app/Filament/Actions/DeleteGuardianAction.php - Uses DetachGuardianFromChildrenAction. Visible only in Guardian panel

### Existing Business Actions (app/Actions/)
- @app/Actions/UpdateChildAction.php - Takes Child + childData array
- @app/Actions/UpdateGuardianAction.php - Takes Guardian + guardianData + optional childrenSyncData
- @app/Actions/SyncChildGuardiansAction.php - Syncs child-guardian relationships
- @app/Actions/CreateOwnershipAction.php - Creates ownership record via updateOrCreate

### Services
- @app/Providers/ServiceServiceProvider.php - Current bindings: AttendanceService, TermAcceptanceService, GatepassService, KeeperInvitationService, SubdomainService

### Panel Providers
- @app/Providers/Filament/KeeperPanelProvider.php - Path: /admin, discovers from app/Filament/Panels/Keeper/, uses RequireOrganizationSubdomain middleware
- @app/Providers/Filament/GuardianPanelProvider.php - Path: /dashboard, discovers from app/Filament/Panels/Guardian/

### Enums
- @app/Enums/Relationship.php - Enum for relationship types (parent, sibling, etc.)

## Important Conventions (from CLAUDE.md)
1. Filament actions must delegate to business actions/services - NO direct model manipulation in Filament actions
2. All classes use `final class` and `declare(strict_types=1)`
3. App Components encapsulate field configuration; only layout concerns are chained in schema arrays
4. Never use `$action` as a variable name (reserved in Filament callbacks)
5. Form schemas separate configuration from layout using App Components
6. The Keeper panel currently scopes children/guardians by organization ownership (`ownedBy($organization)`)
7. NO NEED TO CREATE TESTS FOR NOW

## Key Design Questions
1. How should ownership be checked for keepers? A keeper's User record may or may not be the owner. The `ownership` table tracks `owner_type` and `owner_id` - the owner could be the User directly or the Organization.
2. Should the existing EditChildAction and EditGuardianAction Filament actions be reused in the Keeper panel, or should new ones be created?
3. The Guardian panel forms include relationship management (repeaters for managing guardians/children). Should the Keeper panel forms also include these, or should they use the existing Filament actions?
4. How should the visibility of the edit action be determined - should it check ownership at the action level or use a Filament policy?

## Instructions
You are providing an independent review and implementation plan. Be critical and thorough.
- Read the referenced files to understand the full context
- Analyze the ownership model and how keepers relate to it
- Design a plan that allows keepers to edit children/guardians ONLY if they own those records
- Include relationship management (guardians when editing a child, children when editing a guardian)
- Follow all the conventions from CLAUDE.md (actions/services pattern, Filament guidelines, naming conventions)
- Reuse existing actions and components wherever possible
- Be direct and opinionated -- don't hedge
- Structure your response with clear headings

## Instructions
You are providing an independent second opinion. Be critical and thorough.
- Analyze the question in the context provided
- Identify risks, tradeoffs, and blind spots
- Suggest alternatives if you see better approaches
- Be direct and opinionated — don't hedge
- Structure your response with clear headings
- Keep your response focused and actionable
