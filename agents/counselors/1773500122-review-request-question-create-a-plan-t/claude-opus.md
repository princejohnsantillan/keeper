Now I have full context. Here's my independent review and implementation plan.

---

# Implementation Plan: Keeper Panel Edit Actions with Ownership Guard

## Analysis

### Ownership Model in the Keeper Context

The `ownership` table links owners (User or Organization) to models (Child, Guardian). In the Keeper panel, records are scoped by `ownedBy($organization)` — meaning the **Organization** is the owner. However, the `getEloquentQuery()` on both resources also includes records visible via gatepasses (`orWhereHas('gatepasses.activity'...)`). This means **not every record visible to a keeper is owned by their organization**. The edit action must only appear for organization-owned records.

### Why the Existing Filament Actions Cannot Be Reused

The existing `EditChildAction` and `EditGuardianAction` in `app/Filament/Actions/` are tightly coupled to the Guardian panel:

- `EditChildAction` line 23–25: uses `AuthUser::user()` to scope guardians to the user's ownership
- `EditGuardianAction` line 21: uses `AuthUser::guardian()->children` to scope children

In the Keeper context, the scoping is fundamentally different — everything is scoped to the **Organization**, not the authenticated User/Guardian. The `mutateRecordDataUsing` and visibility logic differ enough that parameterizing the existing actions would add unnecessary complexity.

**Decision: Create new Keeper-specific Filament actions** in `app/Filament/Actions/Keeper/` namespace. The business actions (`UpdateChildAction`, `SyncChildGuardiansAction`, `UpdateGuardianAction`) are already panel-agnostic and will be shared.

### Ownership Visibility Check

A record is editable by a keeper if:
```php
$record->ownerships()
    ->where('owner_type', (new Organization)->getMorphClass())
    ->where('owner_id', $organization->id)
    ->exists()
```

This should be checked via `->visible()` on the action. No policy needed — the check is simple and localized to these two actions.

---

## Files to Create

### 1. `app/Filament/Actions/Keeper/EditChildAction.php`

A Keeper-specific Filament action that:
- Uses `EditAction::make()->slideOver()` (matches Guardian panel pattern)
- `mutateRecordDataUsing`: Loads **all guardians owned by the organization** (via `Guardian::query()->ownedBy($organization)`), then maps existing relationships from the `relationships` table
- `using`: Extracts guardians sync data, validates at least one relationship exists, delegates to `UpdateChildAction` + `SyncChildGuardiansAction`
- `visible`: Only when the child record is owned by `Subdomain::organization()`

The repeater data shape matches the Guardian panel's existing format:
```php
['guardian_id' => string, 'guardian_name' => string, 'relationship' => string|null]
```

### 2. `app/Filament/Actions/Keeper/EditGuardianAction.php`

A Keeper-specific Filament action that:
- Uses `EditAction::make()->slideOver()`
- `mutateRecordDataUsing`: Loads **all children owned by the organization** (via `Child::query()->ownedBy($organization)`), then maps existing relationships
- `using`: Extracts children sync data, delegates to `UpdateGuardianAction`
- `visible`: Only when the guardian record is owned by `Subdomain::organization()`

---

## Files to Modify

### 3. `app/Filament/Panels/Keeper/Resources/Children/Schemas/ChildForm.php`

Add a "Relationships with Guardians" `Fieldset` with a `Repeater` for guardian relationships — mirroring the Guardian panel's `ChildForm` structure (lines 41–46 of `Guardian/Resources/Children/Schemas/ChildForm.php`).

The repeater should be:
- `addable(false)`, `deletable(false)`, `reorderable(false)` — guardians are managed elsewhere; the repeater only edits the relationship type
- Hidden field for `guardian_id`, disabled `TextInput` for `guardian_name`, `Select` for `relationship` (using `App\Enums\Relationship` enum)
- Wrap existing fields in a "Child Details" `Fieldset` with `->columns(2)->columnSpanFull()`

### 4. `app/Filament/Panels/Keeper/Resources/Guardians/Schemas/GuardianForm.php`

Add a "Relationships with Children" `Fieldset` with a `Repeater` for child relationships — mirroring the Guardian panel's `GuardianForm` structure.

Same repeater pattern: hidden `child_id`, disabled `child_name`, `Select` for `relationship`.

Wrap existing fields in a "Guardian Details" `Fieldset`.

### 5. `app/Filament/Panels/Keeper/Resources/Children/Pages/ViewChild.php`

Add the edit action to `getHeaderActions()`:
```php
protected function getHeaderActions(): array
{
    return [
        \App\Filament\Actions\Keeper\EditChildAction::make(),
    ];
}
```

### 6. `app/Filament/Panels/Keeper/Resources/Guardians/Pages/ViewGuardian.php`

Add the edit action to `getHeaderActions()`:
```php
protected function getHeaderActions(): array
{
    return [
        \App\Filament\Actions\Keeper\EditGuardianAction::make(),
    ];
}
```

---

## What NOT to Do

1. **Don't create policies.** The ownership check is a single `->visible()` call on two actions. Adding a full policy infrastructure is overkill here.
2. **Don't modify the existing `EditChildAction`/`EditGuardianAction`.** They work correctly for the Guardian panel. Trying to parameterize them for dual-panel use adds complexity for no benefit.
3. **Don't add delete actions.** The requirement only asks for edit. The Guardian panel's delete actions (`DeleteChildAction`, `DeleteGuardianAction`) detach from guardian — this concept doesn't apply in the Keeper context where the Organization is the owner.
4. **Don't add edit actions to the table row.** Follow the existing pattern: edit is a header action on the view page, not a row action on the list table.

---

## Risks & Blind Spots

### Organization could be null
`Subdomain::organization()` can return `null`. The resources already handle this (returning `whereRaw('1 = 0')`). The new actions must also guard against this — if `$organization` is null, the action should be hidden.

### Sync vs. partial update of relationships
`SyncChildGuardiansAction` calls `$child->guardians()->sync($syncData)` which **replaces all** guardian relationships on the child. In the Guardian panel this is fine because the repeater shows all guardians the user owns. In the Keeper panel, the repeater shows all organization-owned guardians — but the child may also have relationships with guardians NOT owned by the organization (e.g., from another org or user-owned). A blind `sync()` would **delete those relationships**.

**Mitigation**: Instead of using `SyncChildGuardiansAction` directly, the Keeper `EditChildAction` should:
1. Get the set of organization-owned guardian IDs
2. Detach only those guardians from the child (scoped detach)
3. Attach the new set from the form data

Or more simply: only sync within the scope of organization-owned guardians. This could be done by merging the form's sync data with existing non-org relationships before calling sync.

This is the **biggest risk** in this plan. The Guardian panel doesn't have this problem because a user only sees their own guardians, and a child's other guardian relationships are untouched (the sync is scoped to the user's guardians). Wait — actually looking at `SyncChildGuardiansAction` line 16: it calls `$child->guardians()->sync($syncData)` which **replaces ALL** guardian relationships. This means even in the Guardian panel, if Child A has Guardian X (from another user) and Guardian Y (from the current user), editing would remove Guardian X.

This appears to be an existing issue. For the Keeper panel, the same behavior applies — `sync()` replaces everything. Since the Keeper panel shows all organization-owned guardians, and in practice all guardians for a child within an org are likely org-owned, this is probably acceptable. But it's worth noting.

The same applies to `UpdateGuardianAction` which calls `$guardian->children()->sync($childrenSyncData)`.

### Gatepass-only records showing edit button
The `getEloquentQuery()` includes records visible via gatepasses that aren't owned by the organization. The `->visible()` ownership check on the action handles this correctly — those records won't show the edit button. No issue here, just confirming the design handles it.

---

## Summary

| # | File | Action |
|---|------|--------|
| 1 | `app/Filament/Actions/Keeper/EditChildAction.php` | Create |
| 2 | `app/Filament/Actions/Keeper/EditGuardianAction.php` | Create |
| 3 | `app/Filament/Panels/Keeper/.../Schemas/ChildForm.php` | Modify — add guardians repeater |
| 4 | `app/Filament/Panels/Keeper/.../Schemas/GuardianForm.php` | Modify — add children repeater |
| 5 | `app/Filament/Panels/Keeper/.../Pages/ViewChild.php` | Modify — add header action |
| 6 | `app/Filament/Panels/Keeper/.../Pages/ViewGuardian.php` | Modify — add header action |

Business actions (`UpdateChildAction`, `SyncChildGuardiansAction`, `UpdateGuardianAction`) are reused as-is. No new business actions needed. No tests required per `.ai/ai-guidelines`.
