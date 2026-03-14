**Decision**
Do not bolt Keeper edit onto the current actions as-is. Refactor edit flow to be ownership-aware, then reuse it in both panels.

**Critical Findings**
1. Keeper resources intentionally include non-owned records via gatepass/activity joins, so visibility alone is not ownership-safe ([ChildResource](/Users/princejohnsantillan/Herd/keeper/app/Filament/Panels/Keeper/Resources/Children/ChildResource.php#L55), [GuardianResource](/Users/princejohnsantillan/Herd/keeper/app/Filament/Panels/Keeper/Resources/Guardians/GuardianResource.php#L55)).
2. Keeper view pages have no edit entry points yet ([ViewChild](/Users/princejohnsantillan/Herd/keeper/app/Filament/Panels/Keeper/Resources/Children/Pages/ViewChild.php#L14), [ViewGuardian](/Users/princejohnsantillan/Herd/keeper/app/Filament/Panels/Keeper/Resources/Guardians/Pages/ViewGuardian.php#L14)).
3. Existing shared edit actions are Guardian-context hardcoded:
   - `EditChildAction` only pulls guardians owned by `AuthUser::user()` ([EditChildAction](/Users/princejohnsantillan/Herd/keeper/app/Filament/Actions/EditChildAction.php#L23)).
   - `EditGuardianAction` calls `AuthUser::guardian()` which will 403 in Keeper context ([EditGuardianAction](/Users/princejohnsantillan/Herd/keeper/app/Filament/Actions/EditGuardianAction.php#L21), [AuthUser](/Users/princejohnsantillan/Herd/keeper/app/AuthUser.php#L24)).
4. Keeper forms do not include relationship repeaters, so “edit with related children/guardians” is currently impossible ([Keeper ChildForm](/Users/princejohnsantillan/Herd/keeper/app/Filament/Panels/Keeper/Resources/Children/Schemas/ChildForm.php#L17), [Keeper GuardianForm](/Users/princejohnsantillan/Herd/keeper/app/Filament/Panels/Keeper/Resources/Guardians/Schemas/GuardianForm.php#L16)).
5. Ownership model supports polymorphic owners and currently has both `User` and `Organization` owners in data, so Keeper checks must support both owner types ([HasOwnership](/Users/princejohnsantillan/Herd/keeper/app/Models/Concerns/HasOwnership.php#L26), [ownership migration](/Users/princejohnsantillan/Herd/keeper/database/migrations/2026_02_07_003800_create_ownership_table.php#L14)).

**Implementation Plan**
1. Add an ownership access service (interface + implementation) in `app/Services` to centralize:
   - “can current actor edit this child/guardian?”
   - “which owner(s) matched?” (user and/or organization)
   - “fetch editable related children/guardians scoped to matched owner(s)”
2. Bind that interface in [ServiceServiceProvider](/Users/princejohnsantillan/Herd/keeper/app/Providers/ServiceServiceProvider.php#L26).
3. Add `ChildPolicy@update` and `GuardianPolicy@update` using the ownership service. Keep `view` behavior unchanged to preserve current Keeper listing behavior.
4. Refactor existing `EditChildAction` and `EditGuardianAction` to use the ownership service instead of `AuthUser` assumptions. Keep business logic delegation to existing domain actions (`UpdateChildAction`, `SyncChildGuardiansAction`, `UpdateGuardianAction`).
5. Add defensive server-side authorization inside `using()` closures (abort/deny if ownership check fails), not just `visible()`.
6. Update Keeper forms to include relationship repeaters by reusing Guardian form patterns:
   - Child edit includes guardians repeater (as in [Guardian ChildForm](/Users/princejohnsantillan/Herd/keeper/app/Filament/Panels/Guardian/Resources/Children/Schemas/ChildForm.php#L41)).
   - Guardian edit includes children repeater (as in [Guardian GuardianForm](/Users/princejohnsantillan/Herd/keeper/app/Filament/Panels/Guardian/Resources/Guardians/Schemas/GuardianForm.php#L47)).
   - Keep Keeper-specific fields (tags/notes) intact.
7. Add edit header actions to Keeper view pages and gate them by policy/ownership check.
8. Filter sync payload IDs to only IDs in the allowed related set before calling sync actions, preventing cross-owner relationship assignment.

**Answer to Key Design Questions**
1. Ownership check for keepers: allow edit only when ownership row matches either current Keeper’s organization or current authenticated user.
2. Reuse existing edit actions: yes, but only after refactoring them to owner-aware logic; current versions are not Keeper-safe.
3. Relationship management in Keeper forms: yes, add repeaters in Keeper forms so edit modals can manage related guardians/children.
4. Visibility vs policy: use both. Policy is canonical; action-level checks are defense-in-depth.

**Risks / Blind Spots**
1. If you only hide actions and skip server-side checks, forged requests can still mutate records.
2. If you scope related records by “all visible records” instead of matched owners, keepers can create unauthorized cross-owner relationships.
3. Refactoring shared edit actions can regress Guardian panel behavior unless owner-scoping is carefully validated.
