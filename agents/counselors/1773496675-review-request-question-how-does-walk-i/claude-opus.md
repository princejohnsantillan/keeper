Now I have the full picture. Here's my review.

---

# Walk-in Feature Review

## What Works

1. **Clean architecture.** `WalkInRegistrationAction` is a proper business-logic action that delegates to `CreateOwnershipAction`, `GatepassServiceInterface`, and `TermAcceptanceServiceInterface`. The controller is thin. This follows the project's action/service conventions precisely.

2. **DB transaction.** The entire registration (guardian, child, ownership, relationship, term acceptance, gatepass) is wrapped in a single transaction. If anything fails, nothing is left behind.

3. **Terms enforcement is solid.** Server-side validation via `StoreWalkInRegistrationRequest` conditionally requires `agree_to_terms` only when the activity has a term. The action double-checks with `InvalidArgumentException`. The Blade view disables the submit button via JS until the checkbox is checked. Three layers of defense.

4. **Middleware is correct.** `RequireOrganizationSubdomain` ensures the route only works under an org subdomain. `EnsureKeeperAuthenticated` ensures only logged-in keepers can access it. This means a random person can't stumble onto the form — only staff.

5. **Form UX is decent.** Clean Blade template with proper `old()` retention, inline error messages, a top-level error summary, and a success flash. Gender uses styled radio toggle buttons. The form is well-structured with fieldsets.

6. **Validation rules are thorough.** Guardian must be 18+, child birth date must be in the past, string lengths enforced, enums validated via `Rule::enum()`.

7. **Email notification.** `GatepassService::create()` automatically queues a `GatepassCreated` email to the guardian — walk-ins get the same treatment as regular registrations.

---

## What's Broken

1. **No duplicate detection — always creates new guardian/child records.** Every walk-in creates a brand-new `Guardian` and `Child`, even if that exact person already exists in the organization. If the same guardian brings two children to two different activities, you get two separate guardian records. The same child registered by the same guardian at different activities creates duplicate child records. Over time, this pollutes the database with orphaned, disconnected duplicates that are impossible to merge without manual effort.

2. **`AppNotification::walkInRegistered()->send()` fires in an HTTP controller, not a Livewire context.** Filament notifications use Livewire's session-based notification system. In a plain controller redirect, `->send()` pushes to the Livewire session store, but the redirect goes to a Blade page that never reads from Livewire's notification store. **The notification is silently lost.** The flash `session('status')` message works, but the `AppNotification` call is dead code.

3. **Activity route model binding is not scoped to the organization.** The `Activity` model has `#[ScopedBy(OrganizationScope::class)]`, which does filter by the subdomain's org. However, if the `OrganizationScope` silently resolves to `null` for any reason (e.g., timing issue with the subdomain facade), the route could resolve an activity from a different organization. The middleware checks `Subdomain::organization() !== null` but there's no explicit verification that the resolved `Activity` belongs to the current org.

4. **Guardian `birth_date` is required in validation but optional in the controller data array (`?? null`).** The form request says `'guardian_birth_date' => ['required', ...]`, but the controller does `'birth_date' => $validated['guardian_birth_date'] ?? null`. The `?? null` is misleading — it will never trigger because validation already enforces `required`. Not broken per se, but it's a code smell that suggests the intent was for birth_date to be optional. If someone changes the validation to `nullable` later, the form HTML still has `required` — a mismatch waiting to happen.

---

## What Can Be Improved

### High Priority

1. **Duplicate guardian/child detection.** Before creating a new guardian, check if one already exists by email (or email + name) within the organization's ownership scope. If found, reuse it. Same for children: check by name + birth_date + guardian relationship. At minimum, warn the keeper. Better: offer a "link to existing" option. This is the single biggest gap in the feature.

2. **Multi-child registration.** The current form supports exactly one child per submission. In practice, a walk-in guardian often brings multiple children. The keeper has to fill out the entire form repeatedly, re-entering the guardian info each time. Add a repeating "child" section to the form, or at minimum, after successful registration, pre-fill the guardian fields and let them add another child.

3. **Rate limiting / double-submit protection.** The form has no `throttle` middleware and no client-side double-click prevention beyond the terms checkbox logic. A fast double-click on "Register Walk-in" submits two identical POST requests, creating duplicate records. Add `throttle` middleware to the store route and/or a client-side submit-once guard.

4. **Remove the dead `AppNotification::walkInRegistered()->send()` call** from the controller. It does nothing in a non-Livewire context. The flash message already handles success feedback.

### Medium Priority

5. **Activity status check.** There's no validation that the activity is currently active/open for registration. A keeper can walk-in register for a past activity or an unpublished one. Add a check (e.g., `ends_at > now()` or a published status) either in the form request or the action.

6. **The WalkInAction URL opens in the same tab.** When a keeper clicks "Walk-in" from the activities table, they leave Filament entirely and navigate to a standalone Blade page. If they forget to click "back to activities," they're lost. Consider `->openUrlInNewTab()` on the action so Filament stays open.

7. **XSS in terms content.** The terms are rendered with `{!! Str::markdown($activity->term->content ?? '') !!}` — raw HTML output. If the term content contains malicious HTML/JS (e.g., an admin pastes content from elsewhere), it executes. `Str::markdown()` does sanitize to some degree, but consider using `{!! Str::markdown($activity->term->content ?? '', ['html_input' => 'strip']) !!}` or a dedicated sanitizer.

8. **Form request `authorize()` always returns `true`.** The middleware handles authentication, but the form request should also verify the keeper has permission to register walk-ins for this specific activity/organization. This is defense-in-depth.

### Low Priority

9. **No confirmation or receipt shown after registration.** After success, the form just shows a green flash and resets. There's no gatepass code displayed, no printable receipt, no QR code. The keeper has to go back to Filament to find the gatepass. Consider showing the gatepass code/details on the success screen.

10. **The `guardian_email` field has no uniqueness check.** Two walk-ins with the same email create two separate guardian records. Related to the duplicate detection issue above, but even a simple "this email is already registered" warning would help.

11. **The `relationship` type cast in the controller is overly defensive.** The check `$relationshipValue instanceof Relationship ? ... : Relationship::from(...)` is unnecessary — the form request already validates it as `Rule::enum(Relationship::class)`, so it always comes through as a string that can be `from()`'d. Simplify to just `Relationship::from($validated['relationship'])`.

12. **Guardian and Child models are unguarded globally** (`Model::unguard()` in `AppServiceProvider`). This is a project-wide decision, but it means the walk-in action can mass-assign any attribute to Guardian/Child. Since the data comes from the controller (which extracts specific fields from validated input), this is safe in practice. But if someone later passes `$validated` directly, they could set unintended fields.
