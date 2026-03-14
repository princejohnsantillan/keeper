# Walk-ins Feature Analysis

## How it works

The Walk-in feature lets a logged-in Keeper user register a guardian + child pair for an activity on the spot, without requiring the guardian to go through self-registration. The flow is:

1. **Entry point** -- On the Activities table in the Keeper panel, each activity row has a "Walk-in" action button (`WalkInAction`). Clicking it navigates to a standalone Blade form at `/walk-ins/activities/{activity}`.

2. **The form** (`resources/views/keeper/walk-ins/create.blade.php`) -- A full-page, non-Filament HTML form collecting:
   - Guardian: first name, middle name, last name, gender, birth date, phone, email
   - Child: first name, middle name, last name, nickname, gender, birth date, notes
   - Relationship (guardian-to-child)
   - Terms acceptance (only shown if the activity has a `term`)

3. **Submission** -- Posts to `WalkInRegistrationController@store`, validated by `StoreWalkInRegistrationRequest`. Delegates to `WalkInRegistrationAction`, which inside a DB transaction:
   - Creates a new `Guardian` record
   - Creates a new `Child` record
   - Creates `Ownership` records tying both to the activity's organization
   - Attaches guardian to child via pivot with relationship type
   - If activity has terms, records term acceptance
   - Creates a `Gatepass` (which also triggers a `GatepassCreated` email to the guardian)

4. **After submission** -- Redirects back to the same walk-in form with a success flash message and a Filament notification.

---

## What works

- **Clean architecture** -- Business logic is properly extracted into `WalkInRegistrationAction` (delegated from the controller), following the project's action pattern. Dependencies (`CreateOwnershipAction`, `GatepassServiceInterface`, `TermAcceptanceServiceInterface`) are injected.
- **DB transaction** -- The entire operation is wrapped in `DB::transaction()`, so partial failures don't leave orphan records.
- **Terms enforcement** -- If the activity has terms, acceptance is required before the gatepass is created. The form disables the submit button until the checkbox is checked (JS toggle), and server-side validation backs it up with the `accepted` rule.
- **Validation** -- Thorough request validation in `StoreWalkInRegistrationRequest`: required fields, max lengths, enum rules for gender/relationship, guardian must be 18+, child birth date must be in the past.
- **Route protection** -- Routes are behind `RequireOrganizationSubdomain` and `EnsureKeeperAuthenticated` middleware, so only authenticated keepers on a valid org subdomain can access them.
- **Ownership tracking** -- Both the guardian and child are tied to the organization via `CreateOwnershipAction`.
- **Email notification** -- A `GatepassCreated` email is automatically queued to the guardian.
- **Old input retention** -- The Blade form uses `old()` to repopulate fields after validation failure.
- **UX affordance** -- The form redirects back to itself after success, making it easy to register multiple walk-ins in a row.

---

## What's broken / concerning

1. **`authorize()` returns `true` unconditionally** -- `StoreWalkInRegistrationRequest::authorize()` always returns `true`. While the route middleware handles auth, the FormRequest doesn't verify the authenticated keeper actually has access to the specific activity/organization. A keeper from Org A could theoretically POST to Org B's walk-in route if they craft the URL.

2. **`guardian_birth_date` mismatch** -- The validation rule marks `guardian_birth_date` as `required`, but in the controller the data array sets it as `$validated['guardian_birth_date'] ?? null`. This `?? null` fallback is dead code since validation already requires the field -- it's not a bug per se, but it signals a possible intent mismatch (maybe it was supposed to be optional?).

3. **No duplicate detection** -- Every walk-in creates brand new `Guardian` and `Child` records. If the same guardian walks in with the same child for a second activity, they'll have duplicate records in the database. The `GatepassService::findExisting()` method exists but is never called during walk-in registration.

4. **No test coverage** -- There are zero tests for any walk-in functionality (no test files match `WalkIn` or `walk-in` in the tests directory). Given how much this feature does (creating 4+ records in a transaction, emailing, terms acceptance), this is a significant gap.

5. **Filament notification sent outside Filament context** -- `AppNotification::walkInRegistered()->send()` is called in the controller, but this is a Filament notification. It may not display reliably since the walk-in form is a standalone Blade page outside the Filament panel. The `session('status')` flash message is the actual feedback mechanism that works.

---

## What can be improved

1. **Duplicate guardian/child handling** -- Before creating new records, check if a guardian with the same email (or name + birth date) already exists in the organization. Same for children. Offer to reuse existing records or at minimum prevent exact duplicates.

2. **Activity scoping validation** -- The `authorize()` method (or a custom validation rule) should verify the activity belongs to the current organization subdomain and that the activity is currently active/open for registration.

3. **Multi-child support** -- Currently one child per submission. A repeater/dynamic section letting the keeper register multiple children under the same guardian in one go would speed up the common "family walk-in" scenario.

4. **Move to Filament or Livewire** -- The standalone Blade form works but is inconsistent with the rest of the app (Filament panel). A Filament slideOver action or a Livewire component would provide a more cohesive UX and avoid the context-switch of leaving the panel.

5. **Activity status checks** -- No validation that the activity is in a state that accepts registrations (e.g., hasn't ended, isn't cancelled, isn't at capacity). The `Activity` model has `starts_at` and `ends_at` but they're not checked during walk-in.

6. **Test coverage** -- Add feature tests for the controller (happy path, validation failures, terms acceptance flow, middleware guards) and the `WalkInRegistrationAction` (transaction behavior, ownership creation, gatepass creation).

7. **Success feedback** -- After a successful walk-in, showing the generated gatepass code on the success screen (or auto-printing a sticker) would be useful for the keeper operating the registration desk.

8. **Rate limiting** -- No throttle on the POST route. A rapid-fire submission (accidental double-click or malicious) could create duplicate gatepasses.
