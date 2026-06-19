# `last_printed_at` records the explicit "Print Sticker" click, not page-open

## Context

The attendance sticker print page is a Livewire preview that the keeper reaches two ways: by clicking **Print** in the attendance table, and by the automatic redirect that fires right after every successful check-in. The server cannot observe a real physical print — only that the page was requested or that the in-page **Print Sticker** button (which calls `window.print()`) was clicked.

## Decision

`last_printed_at` is set only when the keeper clicks the in-page **Print Sticker** button. Merely opening the preview — including the automatic open on check-in — does not set it.

## Considered Options

- **Set on page-open (in the controller).** Rejected: simplest, but the page auto-opens on every check-in, so `last_printed_at` would collapse into "check-in time" and an opened-then-cancelled preview would read as printed.
- **Set via the browser `afterprint` event.** Rejected: marginally more accurate but `afterprint` also fires on Cancel in most browsers, and it adds JS plus a callback endpoint for almost no real gain over the click.

## Consequences

A keeper can check a child in, land on the auto-opened preview, and `last_printed_at` stays `null` until they actually click **Print Sticker** — which is intended, but surprising if you assume "the print page opened" means "printed." Cancelling the OS print dialog after clicking still counts as printed (we record on click, not on dialog completion).
