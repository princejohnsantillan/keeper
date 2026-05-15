# Context

Glossary of domain terms used in this codebase.

## Activity lifecycle

- **Upcoming activity** — an `Activity` whose `ends_at` is in the future (i.e. `ends_at >= now()`). Includes both activities that haven't started yet and activities currently in progress. The Guardian panel only ever shows upcoming activities; the Keeper panel shows all activities but defaults to ordering upcoming first.
- **Past activity** — an `Activity` whose `ends_at` is in the past. Only visible in the Keeper (admin) panel.
