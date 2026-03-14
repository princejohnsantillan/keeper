# Walk-ins Research

Model: `gpt-5.4`

## Summary
Walk-ins currently work as a keeper-only registration flow that creates a guardian, a child, their relationship, optional term acceptance, and a gatepass for an activity. The flow is usable, but it has important gaps around duplicate prevention, organization scoping, and reliability.

## Current Flow
A keeper clicks the `Walk-in` action from the keeper Activities table.

The action in `app/Filament/Actions/WalkInAction.php` links to the custom walk-in page route defined in `routes/web.php`.

The page is rendered by `app/Http/Controllers/Keeper/WalkInRegistrationController.php` using the Blade view in `resources/views/keeper/walk-ins/create.blade.php`.

On submit, `app/Http/Requests/Keeper/StoreWalkInRegistrationRequest.php` validates the form data, and `app/Actions/WalkInRegistrationAction.php` performs the registration.

That action:
- creates a new guardian
- creates a new child
- creates ownership records for both under the current organization
- attaches the guardian to the child with the selected relationship
- records term acceptance if the activity has terms
- creates a gatepass

After submission, the controller redirects back to the same walk-in form with a success flash message.

Walk-ins do not automatically check a child in. Attendance happens later through the normal gatepass check-in flow.

## What Works
- Keeper access is protected by `RequireOrganizationSubdomain` and `EnsureKeeperAuthenticated` in `routes/web.php`.
- `GetCurrentKeeperAction` ensures the authenticated user is actually a keeper for the current organization.
- The walk-in form collects guardian details, child details, relationship, and activity terms acceptance when required.
- Validation is reasonably solid in `app/Http/Requests/Keeper/StoreWalkInRegistrationRequest.php`.
- Terms acceptance is enforced both in the UI and on the server.
- Successful submissions create the expected records: guardian, child, relationship, optional term acceptance, and gatepass.
- Created gatepasses can be used later in the existing keeper gatepass and scan/check-in flows.

## What Is Broken
- `app/Actions/WalkInRegistrationAction.php` always creates a brand-new guardian and child. There is no attempt to match an existing guardian or child, so repeat submissions create duplicates.
- Walk-ins bypass the duplicate gatepass protection used in the guardian registration flow. `app/Filament/Panels/Guardian/Resources/Activities/Pages/RegisterActivity.php` checks for an existing gatepass before creating one, but the walk-in flow does not.
- Because duplicate people can be created, the same real-world child can end up with multiple records and multiple gatepasses for the same activity.
- That duplicate problem also affects attendance, because `app/Services/AttendanceService.php` only prevents duplicate check-in by `child_id`. If the same child exists twice as two rows, both can be checked in separately.
- Keeper gatepass scoping looks incomplete. `app/Filament/Panels/Keeper/Resources/Gatepasses/GatepassResource.php` does not appear to add organization filtering, unlike keeper child and guardian resources.
- QR/ULID scanning is not organization-scoped. `app/Services/GatepassService.php` resolves ULIDs through `findByUlid()` without filtering by organization.
- Gatepass email queuing is risky inside a transaction. `app/Services/GatepassService.php` queues mail before commit, and `config/queue.php` has `after_commit` set to `false`.
- `AppNotification::walkInRegistered()->send()` is called in the controller, but the walk-in page is a plain Blade page rather than a Filament page, so that notification likely never appears to the user.

## What Can Be Improved
- Reuse existing guardians and children where possible instead of always creating new rows.
- Move duplicate-prevention into shared business logic so guardian self-registration and keeper walk-ins behave the same way.
- After registration, redirect to a more useful next step such as the created gatepass, the gatepass list filtered to the activity, the scan/check-in screen, or a printable confirmation view.
- Add explicit organization scoping to keeper gatepass listings and ULID-based scans.
- Queue gatepass email only after commit, or enable queue `after_commit` where appropriate.
- Add automated tests for the walk-in flow, duplicate prevention, and org scoping.
- Consider moving the walk-in screen into a Filament page so it can reuse shared app components and stay consistent with the rest of the keeper UI.
- If walk-ins should be restricted to active or published activities, add explicit checks for activity availability.

## Notes
This review is based on code inspection and not a live end-to-end test.

## Key Files
- `app/Filament/Actions/WalkInAction.php`
- `routes/web.php`
- `app/Http/Controllers/Keeper/WalkInRegistrationController.php`
- `app/Http/Requests/Keeper/StoreWalkInRegistrationRequest.php`
- `resources/views/keeper/walk-ins/create.blade.php`
- `app/Actions/WalkInRegistrationAction.php`
- `app/Services/GatepassService.php`
- `app/Services/AttendanceService.php`
- `app/Filament/Panels/Guardian/Resources/Activities/Pages/RegisterActivity.php`
- `app/Filament/Panels/Keeper/Pages/ScanGatepass.php`
- `app/Filament/Panels/Keeper/Resources/Gatepasses/GatepassResource.php`
