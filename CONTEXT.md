# Context

Glossary of domain terms used in this codebase.

## Activity lifecycle

- **Upcoming activity** — an `Activity` whose `ends_at` is in the future (i.e. `ends_at >= now()`). Includes both activities that haven't started yet and activities currently in progress. The Guardian panel only ever shows upcoming activities; the Keeper panel shows all activities but defaults to ordering upcoming first.
- **Past activity** — an `Activity` whose `ends_at` is in the past. Only visible in the Keeper (admin) panel.

## Gatepass lifecycle

- **Expired gatepass** — a `Gatepass` whose associated `Activity.ends_at` was more than 24 hours ago. Expired gatepasses are soft-deleted (`deleted_at` set) so they no longer clutter the Guardian dashboard, but remain recoverable for historical lookups. "Expired" is purely time-based — it does not depend on whether the gatepass was checked in or used.
