# Walk-ins: What Works, What's Broken, What Can Be Improved

**Model:** Composer

## Flow Overview

1. **Entry point**: Keeper clicks the "Walk-in" action on the Activities table in Filament.
2. **URL**: Opens `/walk-ins/activities/{activity}` (requires org subdomain + keeper auth).
3. **Form**: Blade view with guardian details, child details, relationship, and optional terms checkbox.
4. **Submit**: Creates Guardian, Child, Ownership, Relationship, Gatepass, and optionally TermAcceptance.
5. **Post-submit**: Redirects back to the same form with success message; `GatepassCreated` email is queued.

---

## What Works

- **End-to-end flow**: Filament → blade form → controller → action.
- **Auth & scoping**: `RequireOrganizationSubdomain` and `EnsureKeeperAuthenticated`; Activity uses `OrganizationScope`.
- **Data creation**: Guardian, Child, Ownership, Relationship, Gatepass, and TermAcceptance (when activity has terms).
- **Validation**: FormRequest validates required fields, guardian age (18+), child birth date, and terms when required.
- **Terms**: Terms shown and required when activity has terms; checkbox enables submit.
- **Email**: `GatepassCreated` mailable queued to guardian email.
- **UX**: Success message, error display, and "back to activities" link.

---

## What's Broken / Problematic

1. **Duplicate guardian/child creation**  
   No lookup for existing guardian or child. Same person registering twice creates duplicate Guardian and Child records.  
   Guardian `RegisterActivity` uses `GatepassService::findExisting()`; walk-in does not.

2. **No duplicate gatepass check**  
   Same guardian + child can be registered multiple times for the same activity, producing multiple gatepasses.

3. **Gatepass code not shown to keeper**  
   Controller ignores the returned Gatepass. Keeper only sees "Walk-in registered successfully" and cannot immediately show or print the gatepass code.

4. **No activity date validation**  
   No check that the activity is in the future or within a registration window. Walk-ins can be created for past activities.

5. **Child born today**  
   Validation uses `before_or_equal:{$today}`, so a child born today may fail depending on timezone/date handling.

---

## What Can Be Improved

1. **Guardian lookup by email**  
   Before creating a Guardian, look up by email within the organization (e.g. via Ownership). Reuse existing Guardian when found.

2. **Child lookup**  
   When guardian exists, offer existing children or allow adding a new one, similar to Guardian `RegisterActivity`.

3. **Duplicate gatepass prevention**  
   Use `GatepassService::findExisting()` before creating a gatepass and show a clear message if already registered.

4. **Show gatepass code after registration**  
   Use the returned Gatepass to display the code (and optionally a print link) on the success screen.

5. **Activity date validation**  
   Add validation (e.g. in FormRequest or controller) that the activity is in the future or within a defined registration period.

6. **Guardian birth date optional**  
   Consider making guardian birth date optional for walk-ins if 18+ is not strictly required.

7. **Form UX**  
   - Add "Register another" after success.  
   - Optionally prefill guardian when reusing an existing one.  
   - Consider a "quick add" flow for repeat walk-ins.

8. **Architecture**  
   - Move mapping of validated data to `guardianData`/`childData` into the action or a dedicated class.  
   - Add a `WalkInRegistrationAction` test.

9. **Access control**  
   - Restrict walk-in to activities the keeper can manage (e.g. policy or scope).  
   - Optionally hide or disable the Walk-in action for past activities.

10. **Notification target**  
    `AppNotification::walkInRegistered()->send()` sends to the current user (keeper). Confirm whether that's intended or if it should go to the guardian.

---

## File Reference

| File | Purpose |
|------|---------|
| `app/Actions/WalkInRegistrationAction.php` | Core registration logic |
| `app/Http/Controllers/Keeper/WalkInRegistrationController.php` | Form display and submit handling |
| `app/Http/Requests/Keeper/StoreWalkInRegistrationRequest.php` | Validation |
| `app/Filament/Actions/WalkInAction.php` | Filament table action |
| `resources/views/keeper/walk-ins/create.blade.php` | Walk-in form view |
| `app/Services/GatepassService.php` | Gatepass creation and `findExisting()` |
