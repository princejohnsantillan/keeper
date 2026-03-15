<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\ActivityStartingSoonGatepassReminderMail;
use App\Models\Gatepass;
use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

final class SendStartingSoonGatepassEmailsAction
{
    public function __invoke(): int
    {
        $windowStart = now();
        $windowEnd = $windowStart->copy()->addMinutes(15);
        $queuedEmails = 0;

        Gatepass::query()
            ->with([
                'activity' => fn (BelongsTo $query): BelongsTo => $query->withoutGlobalScope(OrganizationScope::class),
                'child' => fn (BelongsTo $query): BelongsTo => $query->withTrashed(),
                'guardian' => fn (BelongsTo $query): BelongsTo => $query->withTrashed()->with('user'),
            ])
            ->whereNull('start_reminder_sent_at')
            ->whereHas(
                'activity',
                fn (Builder $query): Builder => $query
                    ->withoutGlobalScope(OrganizationScope::class)
                    ->where('starts_at', '>', $windowStart)
                    ->where('starts_at', '<=', $windowEnd)
            )
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('attendances')
                    ->whereColumn('attendances.activity_id', 'gatepasses.activity_id')
                    ->whereColumn('attendances.child_id', 'gatepasses.child_id')
                    ->whereNotNull('attendances.checked_in_at')
                    ->whereNull('attendances.checked_out_at');
            })
            ->chunkById(100, function (Collection $gatepasses) use (&$queuedEmails): void {
                foreach ($gatepasses as $gatepass) {
                    if (! $gatepass instanceof Gatepass) {
                        continue;
                    }

                    if ($gatepass->guardian === null || $gatepass->child === null || $gatepass->activity === null) {
                        continue;
                    }

                    $email = $gatepass->guardian->user?->email ?? $gatepass->guardian->email;

                    if ($email === null || trim($email) === '') {
                        continue;
                    }

                    Mail::to($email)->queue(new ActivityStartingSoonGatepassReminderMail($gatepass));

                    $gatepass->forceFill([
                        'start_reminder_sent_at' => now(),
                    ])->save();

                    $queuedEmails++;
                }
            });

        return $queuedEmails;
    }
}
